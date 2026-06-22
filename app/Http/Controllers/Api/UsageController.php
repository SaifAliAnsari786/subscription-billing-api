<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsageRequest;
use App\Models\UsageEvent;
use Illuminate\Database\QueryException;
use OpenApi\Attributes as OA;

class UsageController extends Controller
{
    /**
     * Store a customer usage event.
     */

    #[OA\Post(
        path: '/api/usage',
        operationId: 'recordUsage',
        tags: ['Usage Events'],
        summary: 'Record usage event',
        description: 'Store a metered usage event for a customer.',
        security: [['sanctum' => []]]
    )]

    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: [
                'customer_id',
                'metric',
                'quantity',
                'occurred_at',
                'idempotency_key'
            ],
            properties: [
                new OA\Property(
                    property: 'customer_id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'metric',
                    type: 'string',
                    example: 'api_calls'
                ),
                new OA\Property(
                    property: 'quantity',
                    type: 'integer',
                    example: 50
                ),
                new OA\Property(
                    property: 'occurred_at',
                    type: 'string',
                    format: 'date-time',
                    example: '2026-06-22T10:00:00Z'
                ),
                new OA\Property(
                    property: 'idempotency_key',
                    type: 'string',
                    example: 'usage-001'
                ),
            ]
        )
    )]

    #[OA\Response(
        response: 201,
        description: 'Usage event recorded successfully'
    )]

    #[OA\Response(
        response: 200,
        description: 'Usage event already processed'
    )]

    #[OA\Response(
        response: 500,
        description: 'Internal server error'
    )]
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