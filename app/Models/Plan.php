<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'metric',
        'monthly_fee',
        'included_quota',
        'overage_rate',
        'tax_rate',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}