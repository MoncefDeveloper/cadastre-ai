<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Category\CategoryType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // =========================================================================
            // 1. PROPERTY CATEGORIES (IDs 1 - 5)
            // =========================================================================
            [
                'id' => 1,
                'name' => 'Residential Homes & Duplexes',
                'slug' => 'residential-homes-duplexes',
                'description' => 'Modern family villas, townhouses, and duplex residences across prime districts in Algiers, Paris, and suburban corridors.',
                'icon' => 'category-icons/category_placeholder (1).png',
                'color' => '#059669', // Emerald
                'is_active' => true,
                'type' => CategoryType::PROPERTY,
                'parent_id' => null,
            ],
            [
                'id' => 2,
                'name' => 'Luxury Villas & Penthouses',
                'slug' => 'luxury-villas-penthouses',
                'description' => 'High-end architectural estates, private compounds in Hydra/El Biar, Parisian Haussmannian penthouses, and French Riviera sea-view villas.',
                'icon' => 'category-icons/category_placeholder (2).png',
                'color' => '#d97706', // Amber Gold
                'is_active' => true,
                'type' => CategoryType::PROPERTY,
                'parent_id' => 1, // Subcategory of Residential
            ],
            [
                'id' => 3,
                'name' => 'Commercial & Prime Offices',
                'slug' => 'commercial-prime-offices',
                'description' => 'Grade-A corporate headquarters, retail frontage, co-working spaces, and logistics hubs in business centers (Bab Ezzouar, Paris La Défense, Manhattan).',
                'icon' => 'category-icons/category_placeholder (4).png',
                'color' => '#0284c7', // Sky Blue
                'is_active' => true,
                'type' => CategoryType::PROPERTY,
                'parent_id' => null,
            ],
            [
                'id' => 4,
                'name' => 'Mediterranean & Vacation Rentals',
                'slug' => 'vacation-rentals',
                'description' => 'Short-term and seasonal premium rentals located along the Oran coast, Côte d’Azur, and luxury city-break apartments.',
                'icon' => 'category-icons/category_placeholder (3).png',
                'color' => '#7c3aed', // Purple
                'is_active' => true,
                'type' => CategoryType::PROPERTY,
                'parent_id' => null,
            ],
            [
                'id' => 5,
                'name' => 'Land & Development Plots',
                'slug' => 'land-development-plots',
                'description' => 'Zoned residential plots, agricultural estates, and commercial real estate development parcels.',
                'icon' => 'category-icons/category_placeholder (2).png',
                'color' => '#475569', // Slate
                'is_active' => true,
                'type' => CategoryType::PROPERTY,
                'parent_id' => null,
            ],

            // =========================================================================
            // 2. TEMPLATE CATEGORIES (IDs 6 - 7)
            // =========================================================================
            [
                'id' => 6,
                'name' => 'Lead Inquiries & First Contact',
                'slug' => 'lead-inquiries-first-contact',
                'description' => 'AI copilot response blueprints for newly captured web form leads, inbound Postmark emails, and initial property match proposals.',
                'icon' => null,
                'color' => '#2563eb', // Royal Blue
                'is_active' => true,
                'type' => CategoryType::TEMPLATE,
                'parent_id' => null,
            ],
            [
                'id' => 7,
                'name' => 'Compliance & Legal Notices',
                'slug' => 'compliance-legal-notices',
                'description' => 'Legally certified response templates containing disclaimer disclosures, Fair Housing compliance notes, and private viewing agreements.',
                'icon' => null,
                'color' => '#dc2626', // Crimson Red
                'is_active' => true,
                'type' => CategoryType::TEMPLATE,
                'parent_id' => null,
            ],

            // =========================================================================
            // 3. CLIENT TAG CATEGORIES (IDs 8 - 10)
            // =========================================================================
            [
                'id' => 8,
                'name' => 'VIP Diaspora Investor',
                'slug' => 'vip-diaspora-investor',
                'description' => 'High-net-worth buyers and expatriate investors seeking high-yield buy-to-let properties or cross-border assets.',
                'icon' => null,
                'color' => '#b45309', // Warm Bronze
                'is_active' => true,
                'type' => CategoryType::CLIENT_TAG,
                'parent_id' => null,
            ],
            [
                'id' => 9,
                'name' => 'First-Time Homebuyer',
                'slug' => 'first-time-homebuyer',
                'description' => 'Clients purchasing their primary residence requiring step-by-step guidance on payment plans and notary processes.',
                'icon' => null,
                'color' => '#0d9488', // Teal
                'is_active' => true,
                'type' => CategoryType::CLIENT_TAG,
                'parent_id' => null,
            ],
            [
                'id' => 10,
                'name' => 'Corporate Relocation',
                'slug' => 'corporate-relocation',
                'description' => 'Multinational executives and diplomats relocating for long-term leases in embassy and business districts.',
                'icon' => null,
                'color' => '#4338ca', // Indigo
                'is_active' => true,
                'type' => CategoryType::CLIENT_TAG,
                'parent_id' => null,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['id' => $categoryData['id']],
                $categoryData
            );
        }
    }
}
