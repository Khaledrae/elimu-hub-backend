<?php
// app/Services/MpesaService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MpesaService
{
    private $consumerKey;
    private $consumerSecret;
    private $shortcode;
    private $passkey;
    private $callbackUrl;
    private $environment;

    public function __construct()
    {
        $this->consumerKey = env('MPESA_CONSUMER_KEY');
        $this->consumerSecret = env('MPESA_CONSUMER_SECRET');
        $this->shortcode = env('MPESA_SHORTCODE', '174379');
        $this->passkey = env('MPESA_PASSKEY');
        $this->callbackUrl = env('MPESA_CALLBACK_URL');
        $this->environment = env('MPESA_ENV', 'sandbox');
    }

    private function getBaseUrl()
    {

        return $this->environment === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }
    private function generateAccessToken()
    {
        $url = $this->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials';

        Log::info('Generating MPESA token', [
            'url' => $url,
            'consumer_key' => substr($this->consumerKey, 0, 10) . '...',
        ]);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode($this->consumerKey . ':' . $this->consumerSecret)
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Important for local testing
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Important for local testing
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // For debugging, enable verbose output
        if (config('app.debug')) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        if (config('app.debug')) {
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            Log::debug('CURL verbose output', ['log' => $verboseLog]);
        }

        curl_close($ch);

        Log::info('MPESA token response', [
            'http_code' => $httpCode,
            'response_length' => strlen($response),
            'error' => $error,
        ]);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);

            if (isset($data['access_token'])) {
                Log::info('MPESA token generated successfully', [
                    'token_length' => strlen($data['access_token']),
                    'expires_in' => $data['expires_in'] ?? 'unknown'
                ]);
                return $data['access_token'];
            }
        }

        Log::error('Failed to generate MPESA access token', [
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error,
            'consumer_key' => substr($this->consumerKey, 0, 5) . '...',
        ]);

        throw new \Exception('Failed to generate access token. HTTP Code: ' . $httpCode . ' Error: ' . $error);
    }



    private function generateTimestamp()
    {
        return Carbon::now()->format('YmdHis');
    }

    private function generatePassword()
    {
        $timestamp = $this->generateTimestamp();
        
        // IMPORTANT: Debug the password generation
        $passwordString = $this->shortcode . $this->passkey . $timestamp;
        $password = base64_encode($passwordString);
        
        Log::debug('Password Generation', [
            'shortcode' => $this->shortcode,
            'passkey_first_10' => substr($this->passkey, 0, 10) . '...',
            'passkey_length' => strlen($this->passkey),
            'timestamp' => $timestamp,
            'password_string' => $passwordString,
            'password_encoded' => $password,
        ]);
        
        return ['password' => $password, 'timestamp' => $timestamp];
    }

    // C2B (Customer to Business) - Buy Goods
    public function stkPush($phoneNumber, $amount, $accountReference, $transactionDesc)
    {
        try {
            Log::info('Initiating STK Push', [
                'phone' => $phoneNumber,
                'original_amount' => $amount,
                'reference' => $accountReference,
                'desc' => $transactionDesc,
            ]);
            
            $accessToken = $this->generateAccessToken();
            $passwordData = $this->generatePassword();
            
            $url = $this->getBaseUrl() . '/mpesa/stkpush/v1/processrequest';
            $amount = $this->formatAmount($amount);
            
            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $passwordData['password'],
                'Timestamp' => $passwordData['timestamp'],
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $amount,
                'PartyA' => $this->formatPhoneNumber($phoneNumber),
                'PartyB' => $this->shortcode,
                'PhoneNumber' => $this->formatPhoneNumber($phoneNumber),
                'CallBackURL' => $this->callbackUrl,
                'AccountReference' => $accountReference,
                'TransactionDesc' => $transactionDesc,
            ];

            // Log the complete payload for debugging
            Log::info('STK Push Request Payload', [
                'url' => $url,
                'shortcode' => $this->shortcode,
                'timestamp' => $passwordData['timestamp'],
                'password_encoded' => $passwordData['password'],
                'amount' => $amount,
                'phone_formatted' => $this->formatPhoneNumber($phoneNumber),
                'callback_url' => $this->callbackUrl,
                'payload_debug' => $payload, // Log the entire payload
            ]);
            
            $ch = curl_init($url);
            
            $jsonPayload = json_encode($payload);
            
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $jsonPayload,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 30,
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $curlInfo = curl_getinfo($ch);
            
            curl_close($ch);
            
            $responseData = json_decode($response, true);
            
            Log::info('STK Push Response', [
                'http_code' => $httpCode,
                'curl_error' => $error,
                'response_data' => $responseData,
                'raw_response' => $response,
                'curl_info' => $curlInfo,
            ]);
            
            if ($httpCode === 200 && isset($responseData['ResponseCode']) && $responseData['ResponseCode'] === '0') {
                return [
                    'success' => true,
                    'merchant_request_id' => $responseData['MerchantRequestID'],
                    'checkout_request_id' => $responseData['CheckoutRequestID'],
                    'response_description' => $responseData['ResponseDescription'],
                    'customer_message' => $responseData['CustomerMessage'],
                ];
            }
            
            // Detailed error analysis
            $errorMessage = 'STK Push failed';
            if (isset($responseData['errorMessage'])) {
                $errorMessage = $responseData['errorMessage'];
            } elseif (isset($responseData['ResponseDescription'])) {
                $errorMessage = $responseData['ResponseDescription'];
            } elseif ($httpCode !== 200) {
                $errorMessage = 'HTTP Error: ' . $httpCode;
            }
            
            Log::error('STK Push Failed Analysis', [
                'error_message' => $errorMessage,
                'error_code' => $responseData['errorCode'] ?? null,
                'response_code' => $responseData['ResponseCode'] ?? null,
                'request_payload' => $payload, // Include for debugging
                'shortcode_used' => $this->shortcode,
                'passkey_used' => substr($this->passkey, 0, 10) . '...',
            ]);
            
            return [
                'success' => false,
                'error' => $errorMessage,
                'error_code' => $responseData['errorCode'] ?? null,
                'response_code' => $responseData['ResponseCode'] ?? null,
            ];
            
        } catch (\Exception $e) {
            Log::error('STK Push Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    /**
     * Format amount correctly for MPESA
     * Sandbox: Only accepts 1, 10, 50, 100
     * Production: Any integer amount
     */
    private function formatAmount($amount)
    {
        // Convert to integer
        $amount = intval($amount);

        // For sandbox, validate amount
        if ($this->environment === 'sandbox') {
            $validSandboxAmounts = [1, 10, 50, 100];

            // If amount is not valid for sandbox, use the closest valid amount
            if (!in_array($amount, $validSandboxAmounts)) {
                Log::warning('Sandbox amount adjustment', [
                    'original_amount' => $amount,
                    'valid_amounts' => $validSandboxAmounts,
                ]);

                // Find the closest valid amount
                $closest = null;
                $closestDiff = PHP_INT_MAX;

                foreach ($validSandboxAmounts as $validAmount) {
                    $diff = abs($amount - $validAmount);
                    if ($diff < $closestDiff) {
                        $closestDiff = $diff;
                        $closest = $validAmount;
                    }
                }

                $amount = $closest ?? 1; // Default to 1 if no closest found

                Log::info('Amount adjusted for sandbox', [
                    'original' => $amount,
                    'adjusted' => $amount,
                ]);
            }
        }

        return $amount;
    }


    // Check transaction status
    public function checkTransactionStatus($checkoutRequestId)
    {
        try {
            $accessToken = $this->generateAccessToken();
            $passwordData = $this->generatePassword();

            $url = $this->getBaseUrl() . '/mpesa/stkpushquery/v1/query';

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $passwordData['password'],
                'Timestamp' => $passwordData['timestamp'],
                'CheckoutRequestID' => $checkoutRequestId,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Check transaction status failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function formatPhoneNumber($phone)
    {
        // Convert to 2547XXXXXXXX format
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) === 9) {
            return '254' . $phone;
        } elseif (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            return '254' . substr($phone, 1);
        }

        return $phone;
    }
}
