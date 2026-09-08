<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['percentage', 'fixed']);

        return [
            'code' => strtoupper(Str::random(8)),
            'type' => $type,
            'value' => $type === 'percentage'
                ? $this->faker->numberBetween(5, 50)
                : $this->faker->numberBetween(1000, 10000), // Stored in cents (e.g. 10.00 to 100.00)
            'currency' => $type === 'fixed' ? $this->faker->randomElement(['SAR', 'USD']) : null,
            'limit_uses' => $this->faker->optional(0.7)->numberBetween(50, 1000),
            'use_count' => 0,
            'valid_from' => null,
            'valid_until' => null,
            'is_active' => true,
        ];
    }
}
