<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Plan>
     */
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Starter',
                'Pro',
                'Enterprise',
            ]),
            'monthly_fee' => 99.99,
            'included_quota' => 1000,
            'overage_rate' => 0.05,
            'metric' => 'api_calls',
            'tax_rate' => 10,
        ];
    }
}