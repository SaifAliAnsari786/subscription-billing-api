<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user can create a subscription.
     */
    public function test_user_can_create_subscription(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $customer = Customer::factory()->create();

        $plan = Plan::factory()->create();

        $response = $this->postJson('/api/subscriptions', [
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'customer_id',
                    'plan_id',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
        ]);
    }

    /**
     * Test duplicate active subscription is not allowed.
     */
    public function test_duplicate_active_subscription_is_not_allowed(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $customer = Customer::factory()->create();

        $plan = Plan::factory()->create();

        Subscription::create([
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);

        $response = $this->postJson('/api/subscriptions', [
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Customer already has an active subscription.',
            ]);
    }
}