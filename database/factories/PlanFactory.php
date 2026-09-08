<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word() . ' Plan';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'price_monthly' => $this->faker->numberBetween(1900, 49900), // Stored in cents
            'price_yearly' => $this->faker->numberBetween(19900, 499900), // Stored in cents
            'currency' => 'USD',
            'features' => ['1 Agent Seat', 'Basic Matching Engine'],
            'limits' => ['agents' => 1, 'messages_per_month' => 100],
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
            'mock_subscriber_count' => $this->faker->numberBetween(10, 500),
        ];
    }
}
