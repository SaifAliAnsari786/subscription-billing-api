<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\UsageEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user can generate an invoice with base, overage, and tax charges.
     */
    public function test_user_can_generate_invoice_for_subscription(): void
    {
        Sanctum::actingAs(User::factory()->create([
            'role' => 'admin',
        ]));

        $customer = Customer::factory()->create();

        $plan = Plan::factory()->create([
            'monthly_fee' => 100,
            'included_quota' => 100,
            'overage_rate' => 0.50,
            'tax_rate' => 10,
            'metric' => 'api_calls',
        ]);

        $subscription = Subscription::factory()->create([
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
        ]);

        UsageEvent::factory()->create([
            'customer_id' => $customer->id,
            'metric' => 'api_calls',
            'quantity' => 150,
        ]);

        $response = $this->postJson("/api/subscriptions/{$subscription->id}/invoice");

        $response
            ->assertOk()
            ->assertJsonPath('data.subtotal', 125)
            ->assertJsonPath('data.tax', 12.5)
            ->assertJsonPath('data.total', 137.5)
            ->assertJsonCount(2, 'data.items');

        $this->assertDatabaseHas('invoices', [
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subtotal' => 125,
            'tax' => 12.5,
            'total' => 137.5,
        ]);
    }

    /**
     * Test admin can list invoices.
     */
    public function test_admin_can_list_invoices(): void
    {
        Sanctum::actingAs(User::factory()->create([
            'role' => 'admin',
        ]));

        $invoice = $this->createInvoiceForUser();

        $response = $this->getJson('/api/invoices');

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Invoice list retrieved successfully.')
            ->assertJsonFragment([
                'invoice_number' => $invoice->invoice_number,
            ]);
    }

    /**
     * Test customer can view their own invoice.
     */
    public function test_customer_can_view_own_invoice(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $invoice = $this->createInvoiceForUser($user);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/invoices/{$invoice->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $invoice->id)
            ->assertJsonPath('data.customer_id', $invoice->customer_id);
    }

    /**
     * Test customer cannot view another customer's invoice.
     */
    public function test_customer_cannot_view_another_customers_invoice(): void
    {
        $owner = User::factory()->create([
            'role' => 'customer',
        ]);

        $otherCustomer = User::factory()->create([
            'role' => 'customer',
        ]);

        $invoice = $this->createInvoiceForUser($owner);

        Sanctum::actingAs($otherCustomer);

        $response = $this->getJson("/api/invoices/{$invoice->id}");

        $response
            ->assertForbidden()
            ->assertJson([
                'message' => 'You are not authorized to access this invoice.',
            ]);
    }

    /**
     * Create a persisted invoice connected to a customer's user account.
     */
    private function createInvoiceForUser(?User $user = null): Invoice
    {
        $user ??= User::factory()->create([
            'role' => 'customer',
        ]);

        $customer = Customer::factory()->create([
            'user_id' => $user->id,
        ]);

        $subscription = Subscription::factory()->create([
            'customer_id' => $customer->id,
        ]);

        return Invoice::create([
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'invoice_number' => 'INV-'.fake()->unique()->numerify('######'),
            'subtotal' => 100,
            'tax' => 10,
            'total' => 110,
            'billing_start' => now()->startOfMonth(),
            'billing_end' => now()->endOfMonth(),
            'status' => 'generated',
        ]);
    }
}
