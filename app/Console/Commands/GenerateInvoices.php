<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\InvoiceService;
use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:generate-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly invoices for active subscriptions';

    /**
     * Execute the console command.
     */
    public function handle(InvoiceService $invoiceService)
    {
        $subscriptions = Subscription::where('status', 'active')->get();

        if ($subscriptions->isEmpty()) {

            $this->info('No active subscriptions found.');

            return self::SUCCESS;
        }

        foreach ($subscriptions as $subscription) {

            $invoiceService->generate($subscription);
        }

        $this->info('Invoices generated successfully.');

        return self::SUCCESS;
    }
}