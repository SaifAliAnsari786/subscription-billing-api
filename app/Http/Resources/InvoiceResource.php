<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'customer_id' => $this->customer_id,
            'subscription_id' => $this->subscription_id,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'status' => $this->status,
            'billing_start' => $this->billing_start,
            'billing_end' => $this->billing_end,

            'customer' => $this->whenLoaded('customer'),
            'subscription' => $this->whenLoaded('subscription'),
            'items' => $this->whenLoaded('items'),
        ];
    }
}