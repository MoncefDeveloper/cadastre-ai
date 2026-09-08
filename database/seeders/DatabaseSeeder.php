<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Roles & Permissions Foundation
            ShieldSeeder::class,
            UserSeeder::class,

            // 2. Independent Catalogs & Inquiries
            CategorySeeder::class,
            PlanSeeder::class,
            CouponSeeder::class,
            FaqSeeder::class,
            ContactSeeder::class,
            ClientSeeder::class,

            // 3. AI Prompts & Real Estate Inventory
            AiModifierSeeder::class,
            TemplateSeeder::class,
            PropertySeeder::class, // Seeds 10 Properties + 20 Property Images

            // 4. AI Shared Inbox & Matching Engine
            ThreadSeeder::class,
            MessageSeeder::class,
            ThreadPropertyMatchSeeder::class,
        ]);
    }
}
