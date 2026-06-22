<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InvoiceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($invoice) {

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_id' => $invoice->customer_id,
                    'subscription_id' => $invoice->subscription_id,
                    'subtotal' => $invoice->subtotal,
                    'tax' => $invoice->tax,
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                    'billing_start' => $invoice->billing_start,
                    'billing_end' => $invoice->billing_end,
                    'items' => $invoice->items,
                ];

            }),
        ];
    }

    /**
     * Additional response data.
     */
    public function with(Request $request): array
    {
        return [
            'message' => 'Invoice list retrieved successfully.',
        ];
    }
}