<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UsageEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a usage event can be recorded successfully.
     */
    public function test_usage_event_can_be_recorded(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $customer = Customer::factory()->create();

        $response = $this->postJson('/api/usage', [
            'customer_id' => $customer->id,
            'metric' => 'api_calls',
            'quantity' => 100,
            'occurred_at' => now()->toISOString(),
            'idempotency_key' => 'usage-test-001',
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'message' => 'Usage event recorded successfully.',
            ]);

        $this->assertDatabaseHas('usage_events', [
            'customer_id' => $customer->id,
            'idempotency_key' => 'usage-test-001',
        ]);
    }

    /**
     * Test duplicate idempotency key is ignored.
     */
    public function test_duplicate_idempotency_key_is_not_processed_twice(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $customer = Customer::factory()->create();

        $payload = [
            'customer_id' => $customer->id,
            'metric' => 'api_calls',
            'quantity' => 50,
            'occurred_at' => now()->toISOString(),
            'idempotency_key' => 'duplicate-key',
        ];

        $this->postJson('/api/usage', $payload)
            ->assertCreated();

        $this->postJson('/api/usage', $payload)
            ->assertOk()
            ->assertJsonFragment([
                'message' => 'Usage event already processed.',
            ]);

        $this->assertDatabaseCount('usage_events', 1);
    }
}