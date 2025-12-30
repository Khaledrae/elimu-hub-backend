<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'amount',
        'checkout_request_id',
        'merchant_request_id',
        'mpesa_receipt',
        'result_code',
        'result_desc',
        'raw_payload',
        'status'
    ];
}
