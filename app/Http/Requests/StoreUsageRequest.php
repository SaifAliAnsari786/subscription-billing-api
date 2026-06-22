<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsageRequest extends FormRequest
{
    /**
     * Allow all authenticated users.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate incoming usage event.
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'metric' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'occurred_at' => ['required', 'date'],
            'idempotency_key' => ['required', 'string'],
        ];
    }
}