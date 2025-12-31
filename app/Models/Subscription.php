<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model {
    protected $fillable = [
        'user_id',
        'plan_id',
        'amount',
        'status',
        'start_date',
        'end_date',
        'mpesa_receipt_number',
        'transaction_id',
        'payment_details',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'payment_details' => 'array',
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function plan() {
        return $this->belongsTo(Plan::class);
    }
}

