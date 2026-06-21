<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function usageEvents()
    {
        return $this->hasMany(UsageEvent::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}