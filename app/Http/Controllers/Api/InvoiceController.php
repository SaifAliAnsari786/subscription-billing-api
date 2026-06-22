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
        description: 'Retrieve a paginated list of invoices.',
        security: [['sanctum' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'Invoice list retrieved successfully'
    )]
    #[OA\Response(
        response: 403,
        description: 'Forbidden'
    )]
    public function index()
    {
        $query = Invoice::with([
            'customer',
            'subscription',
            'items',
        ]);

        // Customer can only view their own invoices
        if (auth()->user()->role === 'customer') {

            $query->whereHas('customer', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }

        return new InvoiceCollection(
            $query->latest()->paginate(10)
        );
    }

    /**
     * Display a single invoice.
     */
    #[OA\Get(
        path: '/api/invoices/{invoice}',
        operationId: 'showInvoice',
        tags: ['Invoices'],
        summary: 'View invoice details',
        description: 'Retrieve details of a specific invoice.',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(
        name: 'invoice',
        in: 'path',
        required: true,
        schema: new OA\Schema(
            type: 'integer',
            example: 1
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Invoice retrieved successfully'
    )]
    #[OA\Response(
        response: 403,
        description: 'Forbidden'
    )]
    #[OA\Response(
        response: 404,
        description: 'Invoice not found'
    )]
    public function show(Invoice $invoice)
    {
        // Customer can only access their own invoice
        if (
            auth()->user()->role === 'customer' &&
            $invoice->customer->user_id !== auth()->id()
        ) {
            return response()->json([
                'message' => 'You are not authorized to access this invoice.'
            ], 403);
        }

        return new InvoiceResource(
            $invoice->load([
                'customer',
                'subscription',
                'items',
            ])
        );
    }
}