<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageEvent extends Model
{
    protected $fillable = [
        'customer_id',
        'metric',
        'quantity',
        'occurred_at',
        'idempotency_key',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}