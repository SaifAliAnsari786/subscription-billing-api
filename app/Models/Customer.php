<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
    ];

    /**
     * Customer belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Customer subscriptions.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Customer usage events.
     */
    public function usageEvents()
    {
        return $this->hasMany(UsageEvent::class);
    }

    /**
     * Customer invoices.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}