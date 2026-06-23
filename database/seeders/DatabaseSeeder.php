<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Plans
        $this->call([
            PlanSeeder::class,
        ]);

        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Customer User
        $customerUser = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Customer',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        // Customer
        $customer = Customer::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'user_id' => $customerUser->id,
                'name' => 'Customer',
            ]
        );

        // Active Subscription
        Subscription::firstOrCreate(
            [
                'customer_id' => $customer->id,
                'status' => 'active',
            ],
            [
                'plan_id' => Plan::first()->id,
                'starts_at' => now(),
            ]
        );
    }
}