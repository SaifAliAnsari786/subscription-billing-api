<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Subscription;
use OpenApi\Attributes as OA;

class SubscriptionController extends Controller
{
    /**
     * Subscribe a customer to a plan.
     */

    #[OA\Post(
        path: '/api/subscriptions',
        operationId: 'createSubscription',
        tags: ['Subscriptions'],
        summary: 'Create a new subscription',
        description: 'Subscribe a customer to a billing plan.',
        security: [['sanctum' => []]]
    )]

    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['customer_id', 'plan_id'],
            properties: [
                new OA\Property(
                    property: 'customer_id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'plan_id',
                    type: 'integer',
                    example: 1
                ),
            ]
        )
    )]

    #[OA\Response(
        response: 201,
        description: 'Subscription created successfully'
    )]

    #[OA\Response(
        response: 422,
        description: 'Validation failed or customer already has an active subscription'
    )]

    #[OA\Response(
        response: 500,
        description: 'Internal server error'
    )]

    public function store(Request $request)
    {
        try {

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

            // Create subscription
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

        } catch (\Exception $exception) {

            return response()->json([
                'message' => 'Unable to create subscription.',
                'error' => config('app.debug')
                    ? $exception->getMessage()
                    : 'Internal Server Error',
            ], 500);
        }
    }

    /**
     * Cancel subscription at the end of the current billing period.
     */

    #[OA\Post(
        path: '/api/subscriptions/{subscription}/cancel',
        operationId: 'cancelSubscription',
        tags: ['Subscriptions'],
        summary: 'Cancel subscription',
        description: 'Cancel an active subscription at the end of the current billing period.',
        security: [['sanctum' => []]]
    )]

    #[OA\Parameter(
        name: 'subscription',
        description: 'Subscription ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(
            type: 'integer',
            example: 1
        )
    )]

    #[OA\Response(
        response: 200,
        description: 'Subscription cancelled successfully'
    )]

    #[OA\Response(
        response: 422,
        description: 'Subscription already cancelled'
    )]

    #[OA\Response(
        response: 500,
        description: 'Internal server error'
    )]
    public function cancel(Subscription $subscription)
    {
        try {

            // Prevent cancelling an already cancelled subscription
            if ($subscription->status === 'cancelled') {
                return response()->json([
                    'message' => 'Subscription is already cancelled.'
                ], 422);
            }

            // Update subscription
            $subscription->update([
                'status' => 'cancelled',

                // Customer can continue using the service
                // until the current billing period ends.
                'ends_at' => now()->addMonth(),
            ]);

            return response()->json([
                'message' => 'Subscription will be cancelled at the end of the current billing period.',
                'data' => $subscription,
            ], 200);

        } catch (\Exception $exception) {

            return response()->json([
                'message' => 'Unable to cancel subscription.',
                'error' => config('app.debug')
                    ? $exception->getMessage()
                    : 'Internal Server Error',
            ], 500);
        }
    }

    /**
     * Change subscription plan with proration.
     */

    #[OA\Post(
        path: '/api/subscriptions/{subscription}/change-plan',
        operationId: 'changeSubscriptionPlan',
        tags: ['Subscriptions'],
        summary: 'Change subscription plan',
        description: 'Upgrade or downgrade a subscription with proration calculation.',
        security: [['sanctum' => []]]
    )]

    #[OA\Parameter(
        name: 'subscription',
        description: 'Subscription ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(
            type: 'integer',
            example: 1
        )
    )]

    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['plan_id'],
            properties: [
                new OA\Property(
                    property: 'plan_id',
                    type: 'integer',
                    example: 2
                )
            ]
        )
    )]

    #[OA\Response(
        response: 200,
        description: 'Subscription plan updated successfully'
    )]

    #[OA\Response(
        response: 422,
        description: 'Subscription already using selected plan'
    )]

    #[OA\Response(
        response: 500,
        description: 'Internal server error'
    )]
    public function changePlan(Request $request, Subscription $subscription)
    {
        try {

            $data = $request->validate([
                'plan_id' => ['required', 'exists:plans,id'],
            ]);

            $newPlan = Plan::findOrFail($data['plan_id']);

            if ($subscription->plan_id === $newPlan->id) {
                return response()->json([
                    'message' => 'Subscription is already using this plan.'
                ], 422);
            }

            $oldPlan = $subscription->plan;

            $billingCycleDays = 30;
            $usedDays = now()->diffInDays($subscription->starts_at);
            $remainingDays = max(0, $billingCycleDays - $usedDays);

            $remainingCredit = ($oldPlan->monthly_fee / $billingCycleDays) * $remainingDays;
            $remainingCost = ($newPlan->monthly_fee / $billingCycleDays) * $remainingDays;

            $prorationAmount = round($remainingCost - $remainingCredit, 2);

            $subscription->update([
                'plan_id' => $newPlan->id,
            ]);

            return response()->json([
                'message' => 'Subscription plan updated successfully.',
                'proration_amount' => $prorationAmount,
                'data' => $subscription->fresh()->load('plan'),
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'message' => 'Unable to change subscription plan.',
                'error' => config('app.debug')
                    ? $exception->getMessage()
                    : 'Internal Server Error',
            ], 500);
        }
    }
}