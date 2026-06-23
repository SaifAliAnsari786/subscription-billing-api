<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageEvent extends Model
{
    use HasFactory;

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
