<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['code', 'name', 'amount', 'duration_days', 'is_active'];

    public function subscriptions() {
        return $this->hasMany(Subscription::class);
    }
}
