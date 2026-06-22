<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Subscription;
use App\Models\UsageEvent;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Generate invoice for a subscription.
     */
    public function generate(Subscription $subscription)
    {
        return DB::transaction(function () use ($subscription) {

            $plan = $subscription->plan;

            $totalUsage = UsageEvent::where('customer_id', $subscription->customer_id)
                ->where('metric', $plan->metric)
                ->sum('quantity');

            $overageUnits = max(
                0,
                $totalUsage - $plan->included_quota
            );

            $overageAmount = $overageUnits * $plan->overage_rate;

            $subtotal = $plan->monthly_fee + $overageAmount;

            $tax = ($subtotal * $plan->tax_rate) / 100;

            $total = $subtotal + $tax;

            $invoice = Invoice::create([
                'customer_id' => $subscription->customer_id,
                'subscription_id' => $subscription->id,
                'invoice_number' => 'INV-' . now()->timestamp,
                'subtotal' => round($subtotal, 2),
                'tax' => round($tax, 2),
                'total' => round($total, 2),
                'billing_start' => now()->startOfMonth(),
                'billing_end' => now()->endOfMonth(),
                'status' => 'generated',
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $plan->name . ' Monthly Subscription',
                'quantity' => 1,
                'unit_price' => $plan->monthly_fee,
                'amount' => $plan->monthly_fee,
            ]);

            if ($overageUnits > 0) {

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'API Usage Overage',
                    'quantity' => $overageUnits,
                    'unit_price' => $plan->overage_rate,
                    'amount' => $overageAmount,
                ]);
            }

           return $invoice->load('items');

        });
    }
}