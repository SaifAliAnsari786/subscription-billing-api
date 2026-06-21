<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(
            ['name' => 'Basic'],
            [
                'metric' => 'api_calls',
                'monthly_fee' => 29.99,
                'included_quota' => 1000,
                'overage_rate' => 0.0100,
                'tax_rate' => 10.00,
            ]
        );

        Plan::updateOrCreate(
            ['name' => 'Pro'],
            [
                'metric' => 'api_calls',
                'monthly_fee' => 99.99,
                'included_quota' => 10000,
                'overage_rate' => 0.0050,
                'tax_rate' => 10.00,
            ]
        );
    }
}