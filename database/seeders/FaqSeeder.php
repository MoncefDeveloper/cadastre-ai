<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // =========================================================================
            // 1. GLOBAL / PUBLIC AUDIENCE (IDs 1 - 2)
            // =========================================================================
            [
                'id' => 1,
                // 👈 Rebranded to Cadastre AI
                'question' => 'How does Cadastre AI automatically match client inquiries to properties?',
                'answer' => '<p>Cadastre AI analyzes the natural language in incoming client emails via <strong>Google Gemini AI</strong>, extracts key parameters (budget, target city, bedroom count, property type), and computes real-time compatibility scores (85%–98%) against your active inventory.</p>',
                'target_audience' => 'global',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'question' => 'Which geographical real estate markets and currencies are supported?',
                'answer' => '<p>The platform supports cross-border real estate operations spanning <strong>Algeria</strong> (Algiers, Oran, Constantine), <strong>France</strong> (Paris, French Riviera, Lyon), and <strong>US flagship markets</strong> (Miami, New York). Financials seamlessly handle USD, EUR, and DZD.</p>',
                'target_audience' => 'global',
                'is_active' => true,
                'sort_order' => 2,
            ],

            // =========================================================================
            // 2. AGENTS AUDIENCE (IDs 3 - 4)
            // =========================================================================
            [
                'id' => 3,
                'question' => 'How does the Fair Housing (FHA) compliance scoring engine protect agents?',
                // 👈 Rebranded to Cadastre AI's advisory engine
                'answer' => '<p>Before any outbound email is dispatched, Cadastre AI\'s advisory engine evaluates drafts against Fair Housing and anti-discrimination standards. Unless an agent holds the <code>bypass_compliance_gate</code> permission, drafts must achieve an approved compliance rating before sending.</p>',
                'target_audience' => 'agents',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'id' => 4,
                'question' => 'What is the difference between Global and Personal AI Modifiers?',
                'answer' => '<p><strong>Global Modifiers</strong> are agency-wide prompt shortcuts (e.g., <em>Make it Shorter</em>, <em>Emphasize Luxury</em>) managed by Broker Managers. <strong>Personal Modifiers</strong> are private custom shortcuts created by individual agents to fit their personal writing style.</p>',
                'target_audience' => 'agents',
                'is_active' => true,
                'sort_order' => 4,
            ],

            // =========================================================================
            // 3. BILLING / SUBSCRIPTIONS AUDIENCE (IDs 5 - 6)
            // =========================================================================
            [
                'id' => 5,
                'question' => 'Can agencies upgrade or switch subscription tiers mid-cycle?',
                'answer' => '<p>Yes. Agencies can upgrade between <strong>Starter Agent</strong> ($49/mo), <strong>Professional Broker</strong> ($149/mo), and <strong>Enterprise Agency</strong> ($399/mo) at any time with automated prorated billing and instant team seat provisioning.</p>',
                'target_audience' => 'billing',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'id' => 6,
                'question' => 'How do promotional discount coupons work during checkout?',
                'answer' => '<p>Promotional codes (such as <code>LAUNCH50</code> or <code>SAVE100</code>) apply percentage or fixed-amount deductions to monthly or yearly subscriptions. Each coupon verifies usage limits and expiration timestamps automatically upon checkout.</p>',
                'target_audience' => 'billing',
                'is_active' => true,
                'sort_order' => 6,
            ],

            [
                'id' => 7,
                'question' => 'Does Cadastre AI replace our existing email client, or does it integrate with Gmail and Outlook?',
                'answer' => '<p>Cadastre AI integrates non-invasively alongside your existing email infrastructure. Through <strong>Postmark inbound webhooks</strong> and cryptographic mailbox hashing (<code>th_*</code>), client emails sent from Gmail, Outlook, or web forms are triaged and drafted automatically without requiring agents to abandon their corporate email domains.</p>',
                'target_audience' => 'global',
                'is_active' => true,
                'sort_order' => 7,
            ],

            [
                'id' => 8,
                'question' => 'How does the platform protect confidential off-market dossiers and investor privacy?',
                'answer' => '<p>Confidentiality is enforced at both the database and AI prompt level. High-net-worth investor dossiers, acquisition budgets, and off-market deeds are isolated via <strong>Spatie Shield</strong> role authorization. Furthermore, Gemini API calls operate statelessly—your proprietary portfolio data and communications are <strong>never used to train public foundation models</strong>.</p>',
                'target_audience' => 'global',
                'is_active' => true,
                'sort_order' => 8,
            ],

            [
                'id' => 9,
                'question' => 'How does the AI copilot differentiate between cold leads and returning private clients?',
                'answer' => '<p>The copilot queries historical interaction threads in real time. For first-time inquiries, the AI introduces <strong>Cadastre Private Office</strong> and establishes brand authority. For returning clients, it suppresses corporate pleasantries and transitions directly into actionable next steps—such as proposing private viewing schedules or coordinating notary escrow documentation.</p>',
                'target_audience' => 'global',
                'is_active' => true,
                'sort_order' => 9,
            ],

            [
                'id' => 10,
                'question' => 'Can our brokerage team simulate realistic inbound lead traffic before going live?',
                'answer' => '<p>Yes. The platform includes an interactive <strong>Inbound Lead Simulator</strong> supporting custom inquiry payloads as well as curated diplomat/investor presets (Paris Haussmannian, Hydra Diplomatic, Miami Waterfront). This allows managing brokers to test real-time AI criteria extraction and inventory matching safely before onboarding live email streams.</p>',
                'target_audience' => 'global',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($faqs as $faqData) {
            Faq::updateOrCreate(
                ['id' => $faqData['id']],
                $faqData
            );
        }
    }
}
