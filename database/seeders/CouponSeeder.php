<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            // =========================================================================
            // 1. Standard Active Percentage Coupon (20% Off)
            // =========================================================================
            [
                'id' => 1,
                'code' => 'WELCOME20',
                'type' => 'percentage',
                'value' => 20, // 20%
                'currency' => null,
                'limit_uses' => 500,
                'use_count' => 42,
                'valid_from' => now()->subMonths(2),
                'valid_until' => now()->addYear(),
                'is_active' => true,
            ],

            // =========================================================================
            // 2. Fixed USD Discount ($100.00 Off)
            // =========================================================================
            [
                'id' => 2,
                'code' => 'SAVE100',
                'type' => 'fixed',
                'value' => 100 * 100, // $100.00 stored in cents (10,000)
                'currency' => 'USD',
                'limit_uses' => 200,
                'use_count' => 18,
                'valid_from' => now()->subMonth(),
                'valid_until' => now()->addMonths(6),
                'is_active' => true,
            ],

            // =========================================================================
            // 3. Heavy B2B Launch Percentage Promotion (50% Off)
            // =========================================================================
            [
                'id' => 3,
                'code' => 'LAUNCH50',
                'type' => 'percentage',
                'value' => 50, // 50%
                'currency' => null,
                'limit_uses' => 100,
                'use_count' => 35,
                'valid_from' => now()->subWeeks(2),
                'valid_until' => now()->addMonths(3),
                'is_active' => true,
            ],

            // =========================================================================
            // 4. Exclusive VIP Enterprise Fixed Voucher ($500.00 Off)
            // =========================================================================
            [
                'id' => 4,
                'code' => 'ENTERPRISE500',
                'type' => 'fixed',
                'value' => 500 * 100, // $500.00 stored in cents (50,000)
                'currency' => 'USD',
                'limit_uses' => 25,
                'use_count' => 3,
                'valid_from' => now()->subDays(10),
                'valid_until' => now()->addYear(),
                'is_active' => true,
            ],

            // =========================================================================
            // 5. Expired Campaign Coupon (For testing isValid() boundary failure)
            // =========================================================================
            [
                'id' => 5,
                'code' => 'EXPIRED2025',
                'type' => 'percentage',
                'value' => 15, // 15%
                'currency' => null,
                'limit_uses' => 1000,
                'use_count' => 620,
                'valid_from' => now()->subYears(1),
                'valid_until' => now()->subDays(5), // Expired
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::updateOrCreate(
                ['id' => $couponData['id']],
                $couponData
            );
        }
    }
}
