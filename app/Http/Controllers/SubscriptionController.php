<?php
// app/Http/Controllers/SubscriptionController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\MpesaTransaction;
use App\Models\Plan;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
        // $this->middleware('auth:sanctum');
    }

    // Get available plans
    public function getPlans()
    {
        $planPrices = Plan::orderBy('name', 'asc')
            ->where('is_active', true)
            ->get();
        return response()->json([
            'plans' => $planPrices
        ]);
    }

    // Initiate M-Pesa payment
    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'phone_number' => 'required|string|min:10|max:12',
        ]);

        $user = Auth::user();
        $planPrices = Plan::where('id', $validated['plan_id'])->first();
        if (!$planPrices) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid plan selected'
            ], 400);
        }

        $amount = $planPrices->price;
        $accountReference = 'ELIMU' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
        $transactionDesc = "ElimuHub {$planPrices->name} subscription";

        // Initiate STK Push
        $result = $this->mpesaService->stkPush(
            $validated['phone_number'],
            $amount,
            $accountReference,
            $transactionDesc
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 400);
        }

        // Save transaction record
        $transaction = MpesaTransaction::create([
            'user_id' => $user->id,
            'merchant_request_id' => $result['merchant_request_id'],
            'checkout_request_id' => $result['checkout_request_id'],
            'amount' => $amount,
            'phone_number' => $validated['phone_number'],
            'status' => 'pending',
            'response_data' => $result,
        ]);

        // Create pending subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan' => $validated['plan'],
            'amount' => $amount,
            'status' => 'pending',
            'start_date' => Carbon::now(),
            'transaction_id' => $transaction->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment initiated. Please check your phone to complete payment.',
            'checkout_request_id' => $result['checkout_request_id'],
            'transaction' => [
                'id' => $transaction->id,
                'amount' => $amount,
                'phone_number' => $validated['phone_number'],
            ]
        ]);
    }

    // M-Pesa callback endpoint (Webhook)
    public function mpesaCallback(Request $request)
    {
        $callbackData = $request->all();

        Log::info('M-Pesa Callback Received', $callbackData);

        // Safaricom sends data in nested structure
        $body = $callbackData['Body'] ?? $callbackData;
        $stkCallback = $body['stkCallback'] ?? [];

        $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
        $resultCode = $stkCallback['ResultCode'] ?? null;
        $resultDesc = $stkCallback['ResultDesc'] ?? '';
        $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];

        if (!$checkoutRequestId) {
            Log::error('M-Pesa callback missing checkout request ID');
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Failed']);
        }

        // Find transaction
        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$transaction) {
            Log::error('Transaction not found for checkout ID: ' . $checkoutRequestId);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Transaction not found']);
        }

        // Update transaction based on result
        if ($resultCode == 0) {
            // Payment successful
            $mpesaReceipt = '';
            $phoneNumber = '';
            $amount = 0;
            $transactionDate = '';

            // Extract data from callback metadata
            foreach ($callbackMetadata as $item) {
                if ($item['Name'] == 'MpesaReceiptNumber') {
                    $mpesaReceipt = $item['Value'];
                } elseif ($item['Name'] == 'PhoneNumber') {
                    $phoneNumber = $item['Value'];
                } elseif ($item['Name'] == 'Amount') {
                    $amount = $item['Value'];
                } elseif ($item['Name'] == 'TransactionDate') {
                    $transactionDate = $item['Value'];
                }
            }

            DB::transaction(function () use ($transaction, $mpesaReceipt, $resultDesc, $amount) {
                // Update transaction
                $transaction->update([
                    'mpesa_receipt_number' => $mpesaReceipt,
                    'status' => 'completed',
                    'result_description' => $resultDesc,
                    'response_data' => array_merge($transaction->response_data ?? [], ['callback_data' => $callbackData]),
                ]);

                // Activate subscription
                $subscription = Subscription::where('transaction_id', $transaction->id)->first();
                if ($subscription) {
                    $user = $subscription->user;

                    // Calculate end date based on plan
                    $endDate = null;
                    if ($subscription->plan == 'monthly') {
                        $endDate = Carbon::now()->addDays(30);
                    } elseif ($subscription->plan == 'yearly') {
                        $endDate = Carbon::now()->addDays(365);
                    } elseif ($subscription->plan == 'lifetime') {
                        $endDate = null; // Lifetime
                    }

                    $subscription->update([
                        'status' => 'active',
                        'mpesa_receipt_number' => $mpesaReceipt,
                        'end_date' => $endDate,
                    ]);

                    // Update user subscription status
                    $user->update([
                        'subscription_status' => 'premium',
                        'subscription_expires_at' => $endDate,
                        'mpesa_phone' => $transaction->phone_number,
                        'daily_lesson_limit' => 999, // Unlimited for premium
                        'can_access_all_classes' => true,
                    ]);

                    // Send notification to user
                    //$user->notify(new SubscriptionActivatedNotification($subscription));
                }
            });

            Log::info('Subscription activated successfully', [
                'user_id' => $transaction->user_id,
                'receipt' => $mpesaReceipt
            ]);
        } else {
            // Payment failed
            $transaction->update([
                'status' => 'failed',
                'result_description' => $resultDesc,
                'response_data' => array_merge($transaction->response_data ?? [], ['callback_data' => $callbackData]),
            ]);

            $subscription = Subscription::where('transaction_id', $transaction->id)->first();
            if ($subscription) {
                $subscription->update(['status' => 'cancelled']);
            }

            Log::warning('M-Pesa payment failed', [
                'checkout_request_id' => $checkoutRequestId,
                'result_desc' => $resultDesc
            ]);
        }

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Success'
        ]);
    }

    // Check payment status (for frontend polling)
    public function checkPaymentStatus($checkoutRequestId)
    {
        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        // If still pending, check with M-Pesa
        if ($transaction->status === 'pending') {
            $statusResult = $this->mpesaService->checkTransactionStatus($checkoutRequestId);

            if ($statusResult && isset($statusResult['ResultCode'])) {
                if ($statusResult['ResultCode'] == '0') {
                    // Payment completed via query
                    $transaction->update(['status' => 'completed']);
                } elseif ($statusResult['ResultCode'] !== '1032') { // 1032 = Request is processing
                    $transaction->update(['status' => 'failed']);
                }
            }
        }

        return response()->json([
            'success' => true,
            'status' => $transaction->status,
            'transaction' => [
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'phone_number' => $transaction->phone_number,
                'created_at' => $transaction->created_at,
            ],
            'subscription' => $transaction->status === 'completed' ? [
                'status' => 'active',
                'plan' => $transaction->subscription->plan ?? null,
                'expires_at' => $transaction->user->subscription_expires_at ?? null,
            ] : null
        ]);
    }
    // Add these to your M-Pesa callback controller
    public function validateCallback(Request $request)
    {
        // Validate origin IP (optional but recommended)
        $allowedIPs = ['196.201.214.200', '196.201.214.206']; // Safaricom IPs
        $clientIP = $request->ip();

        // if (!in_array($clientIP, $allowedIPs)) {
        //     Log::warning('Unauthorized callback IP', ['ip' => $clientIP]);
        //     return false;
        // }

        // Validate required fields
        $requiredFields = ['Body', 'stkCallback'];
        foreach ($requiredFields as $field) {
            if ($request->input($field) == null) {
                Log::error('Missing required field in callback', ['field' => $field]);
                return false;
            }
        }

        return true;
    }
    // Get user's subscription details
    public function getSubscriptionDetails()
    {
        $user = Auth::user();

        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        $subscriptions = Subscription::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'current_status' => $user->subscription_status,
            'expires_at' => $user->subscription_expires_at,
            'daily_lesson_limit' => $user->daily_lesson_limit,
            'can_access_all_classes' => $user->can_access_all_classes,
            'active_subscription' => $activeSubscription,
            'subscription_history' => $subscriptions,
        ]);
    }

    // Cancel subscription (only for recurring)
    public function cancelSubscription()
    {
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found'
            ], 404);
        }

        // Mark for cancellation at end of period
        $subscription->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Subscription will remain active until ' .
                Carbon::parse($subscription->end_date)->format('M d, Y')
        ]);
    }
}
