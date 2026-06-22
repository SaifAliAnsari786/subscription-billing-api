<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\UsageEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UsageEventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\UsageEvent>
     */
    protected $model = UsageEvent::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'metric' => 'api_calls',
            'quantity' => fake()->numberBetween(1, 100),
            'occurred_at' => now(),
            'idempotency_key' => (string) Str::uuid(),
        ];
    }
}