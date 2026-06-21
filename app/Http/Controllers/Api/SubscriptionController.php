<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    // Subscribe customer to a plan
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        // Check if customer already has an active subscription
        $activeSubscription = Subscription::where('customer_id', $data['customer_id'])
            ->where('status', 'active')
            ->exists();

        if ($activeSubscription) {
            return response()->json([
                'message' => 'Customer already has an active subscription.'
            ], 422);
        }

        // Create new subscription
        $subscription = Subscription::create([
            'customer_id' => $data['customer_id'],
            'plan_id' => $data['plan_id'],
            'status' => 'active',
            'starts_at' => now(),
        ]);

        return response()->json([
            'message' => 'Subscription created successfully.',
            'data' => $subscription,
        ], 201);
    }

    // Cancel subscription at period end
    public function cancel(Request $request, $subscription)
    {
        //
    }

    // Change plan with proration
    public function changePlan(Request $request, $subscription)
    {
        //
    }
}