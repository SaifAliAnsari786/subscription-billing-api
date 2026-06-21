<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'subscription_id',
        'invoice_number',
        'subtotal',
        'tax',
        'total',
        'billing_start',
        'billing_end',
        'status',
    ];

    protected $casts = [
        'billing_start' => 'date',
        'billing_end' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}