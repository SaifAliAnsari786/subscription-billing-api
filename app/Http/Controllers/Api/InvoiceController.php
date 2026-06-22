<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Subscription;
use App\Services\InvoiceService;
use App\Models\Invoice;
use App\Http\Resources\InvoiceCollection;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Generate invoice for a subscription.
     */
    public function generate(Subscription $subscription)
    {
        $invoice = $this->invoiceService->generate($subscription);

        return new InvoiceResource($invoice);
    }

    /**
     * Display a listing of invoices.
     */
    public function index()
    {
        $invoices = Invoice::with(['customer', 'subscription', 'items'])
            ->latest()
            ->paginate(10);

        return new InvoiceCollection($invoices);
    }
}