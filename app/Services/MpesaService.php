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
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        return ['password' => $password, 'timestamp' => $timestamp];
    }

    // C2B (Customer to Business) - Buy Goods
    public function stkPush($phoneNumber, $amount, $accountReference, $transactionDesc)
    {
        try {
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

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $responseData = $response->json();

            if ($response->successful() && $responseData['ResponseCode'] == '0') {
                return [
                    'success' => true,
                    'merchant_request_id' => $responseData['MerchantRequestID'],
                    'checkout_request_id' => $responseData['CheckoutRequestID'],
                    'response_description' => $responseData['ResponseDescription'],
                    'customer_message' => $responseData['CustomerMessage']
                ];
            }

            Log::error('STK Push failed', ['response' => $responseData]);
            return [
                'success' => false,
                'error' => $responseData['errorMessage'] ?? 'STK Push failed'
            ];
        } catch (\Exception $e) {
            Log::error('STK Push exception', ['error' => $e->getMessage()]);
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
