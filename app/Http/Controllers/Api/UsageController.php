<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsageRequest;
use App\Models\UsageEvent;
use Illuminate\Database\QueryException;

class UsageController extends Controller
{
    /**
     * Store a customer usage event.
     */
    public function store(StoreUsageRequest $request)
    {
        try {

            // Create a new usage event
            $usageEvent = UsageEvent::create(
                $request->validated()
            );

            return response()->json([
                'message' => 'Usage event recorded successfully.',
                'data' => $usageEvent,
            ], 201);

        } catch (QueryException $exception) {

            // Duplicate idempotency key detected
            if ($exception->getCode() == 23000) {

                $usageEvent = UsageEvent::where(
                    'idempotency_key',
                    $request->idempotency_key
                )->first();

                return response()->json([
                    'message' => 'Usage event already processed.',
                    'data' => $usageEvent,
                ], 200);
            }

            throw $exception;
        }
    }
}