<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'id' => 1,
                'name' => 'Starter Agent',
                'slug' => 'starter-agent',
                'description' => 'Essential AI shared inbox copilot and listing management for solo agents and boutique brokers.',
                'price_monthly' => 49 * 100,  // $49.00 in cents
                'price_yearly' => 490 * 100,  // $490.00 in cents (2 months free)
                'currency' => 'USD',
                'features' => [
                    'Up to 50 AI Draft Generations / month',
                    '10 Active Property Listings',
                    'Postmark Inbound Email Routing',
                    'Standard Lead & Client CRM',
                    'Basic FHA Compliance Checks',
                    'Single Agent Seat',
                ],
                'limits' => [
                    'ai_drafts_monthly' => 50,
                    'active_listings' => 10,
                    'team_seats' => 1,
                    'storage_mb' => 500,
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
                'mock_subscriber_count' => 214,
            ],
            [
                'id' => 2,
                'name' => 'Professional Broker',
                'slug' => 'professional-broker',
                'description' => 'Complete AI shared inbox, unlimited draft refinements, and team collaboration for growing real estate agencies.',
                'price_monthly' => 149 * 100, // $149.00 in cents
                'price_yearly' => 1490 * 100, // $1,490.00 in cents
                'currency' => 'USD',
                'features' => [
                    'Unlimited AI Copilot Drafts',
                    '50 Active Property Listings',
                    'Postmark & Webhook Multichannel Inbound',
                    'Advanced AI Prompt Modifiers (Custom Tones)',
                    'Strict FHA Compliance Auditing & Scoring',
                    '3 Team Agent Seats Included',
                    'Dedicated Priority Email Support',
                ],
                'limits' => [
                    'ai_drafts_monthly' => -1, // Unlimited
                    'active_listings' => 50,
                    'team_seats' => 3,
                    'storage_mb' => 2048,
                ],
                'is_active' => true,
                'is_featured' => true, // Featured plan
                'sort_order' => 2,
                'mock_subscriber_count' => 640,
            ],
            [
                'id' => 3,
                'name' => 'Enterprise Agency',
                'slug' => 'enterprise-agency',
                'description' => 'High-scale agency solution with custom Gemini AI prompt engineering, unlimited seats, and compliance oversight.',
                'price_monthly' => 399 * 100, // $399.00 in cents
                'price_yearly' => 3990 * 100, // $3,990.00 in cents
                'currency' => 'USD',
                'features' => [
                    'Unlimited AI Drafts with Custom Gemini Model Tuning',
                    'Unlimited Active Property Listings',
                    'Custom AI Modifiers & Legally Binding Templates',
                    'Full Regulatory Compliance Suite & Session Audit',
                    'Unlimited Team Seats & Role Delegation',
                    'Dedicated Postmark Inbound Server & 99.9% SLA',
                    'White-Glove Notary Onboarding',
                ],
                'limits' => [
                    'ai_drafts_monthly' => -1, // Unlimited
                    'active_listings' => -1,   // Unlimited
                    'team_seats' => -1,        // Unlimited
                    'storage_mb' => 10240,
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'mock_subscriber_count' => 118,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['id' => $planData['id']],
                $planData
            );
        }
    }
}
