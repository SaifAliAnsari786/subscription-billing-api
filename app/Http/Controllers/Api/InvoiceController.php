<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Services\InvoiceService;
use OpenApi\Attributes as OA;

class InvoiceController extends Controller
{
    /**
     * Invoice service instance.
     */
    protected InvoiceService $invoiceService;

    /**
     * Create a new controller instance.
     */
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Generate invoice for a subscription.
     */
    #[OA\Post(
        path: '/api/subscriptions/{subscription}/invoice',
        operationId: 'generateInvoice',
        tags: ['Invoices'],
        summary: 'Generate subscription invoice',
        description: 'Generate a billing invoice for the selected subscription.',
        security: [['sanctum' => []]]
    )]

    #[OA\Parameter(
        name: 'subscription',
        in: 'path',
        required: true,
        description: 'Subscription ID',
        schema: new OA\Schema(
            type: 'integer',
            example: 1
        )
    )]

    #[OA\Response(
        response: 200,
        description: 'Invoice generated successfully'
    )]

    #[OA\Response(
        response: 404,
        description: 'Subscription not found'
    )]

    #[OA\Response(
        response: 500,
        description: 'Internal server error'
    )]
    public function generate(Subscription $subscription)
    {
        $invoice = $this->invoiceService->generate($subscription);

        return new InvoiceResource($invoice);
    }

    /**
     * Display a paginated list of invoices.
     */
    #[OA\Get(
        path: '/api/invoices',
        operationId: 'listInvoices',
        tags: ['Invoices'],
        summary: 'List invoices',
        description: 'Retrieve a paginated list of generated invoices.',
        security: [['sanctum' => []]]
    )]

    #[OA\Parameter(
        name: 'page',
        in: 'query',
        required: false,
        description: 'Page number',
        schema: new OA\Schema(
            type: 'integer',
            example: 1
        )
    )]

    #[OA\Response(
        response: 200,
        description: 'Invoice list retrieved successfully'
    )]

    #[OA\Response(
        response: 500,
        description: 'Internal server error'
    )]
    public function index()
    {
        $invoices = Invoice::with([
            'customer',
            'subscription',
            'items'
        ])
        ->latest()
        ->paginate(10);

        return new InvoiceCollection($invoices);
    }
}