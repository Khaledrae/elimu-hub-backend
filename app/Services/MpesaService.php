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
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->shortcode = config('services.mpesa.shortcode');
        $this->passkey = config('services.mpesa.passkey');
        $this->callbackUrl = config('services.mpesa.callback_url');
        $this->environment = config('services.mpesa.env', 'sandbox');
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

        $response = Http::withBasicAuth(
            trim($this->consumerKey),
            trim($this->consumerSecret)
        )
        ->acceptJson()
        ->get($url);

        if ($response->successful()) {
            $data = $response->json();
            return $data['access_token'];
        }

        Log::error('MPESA TOKEN RAW RESPONSE', [
            'status' => $response->status(),
            'body'   => $response->body(),
            'headers' => $response->headers(),
        ]);
        throw new \Exception('Failed to generate access token');
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
