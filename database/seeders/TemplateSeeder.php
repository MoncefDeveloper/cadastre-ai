<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Thread\ThreadChannel;
use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // =========================================================================
            // 1. INQUIRIES & LEAD PROPOSALS (Category ID 6 - Channel: EMAIL)
            // =========================================================================
            [
                'id' => 1,
                'category_id' => 6, // Lead Inquiries & First Contact
                'name' => 'VIP Lead Greeting & Curated Property Match',
                'channel' => ThreadChannel::EMAIL,
                'system_instructions' => 'You are an elite private real estate advisor. Maintain a sophisticated, warm, and highly professional tone. Highlight architectural pedigree, privacy, and investment stability.',
                'prompt' => "Compose a personalized introduction email to {{client_name}}.\n\n1. Thank them for contacting MatchMaker Real Estate regarding their search in {{target_city}}.\n2. Present the curated property recommendation: {{property_title}}.\n3. Highlight key features: {{property_highlights}} and pricing at {{property_price}}.\n4. Conclude with a warm invitation for a confidential private consultation with {{agent_name}}.",
                'variables' => ['client_name', 'target_city', 'property_title', 'property_highlights', 'property_price', 'agent_name'],
                'rules' => [
                    'tone' => 'luxury_persuasive',
                    'max_words' => '200',
                    'fha_compliance' => 'strict',
                ],
                'is_active' => true,
            ],
            [
                'id' => 2,
                'category_id' => 6,
                'name' => 'Private On-Site Viewing Confirmation',
                'channel' => ThreadChannel::EMAIL,
                'system_instructions' => 'You are a senior real estate logistics coordinator. Be punctual, precise, and courteous. Provide clear access and concierge directions.',
                'prompt' => "Draft a viewing appointment confirmation to {{client_name}} for {{property_title}} located at {{property_address}}.\n\n* Date & Time: {{viewing_time}}\n* Meeting Agent: {{agent_name}}\n* Parking & Security: Inform them that security clearance has been arranged at the main gate and private parking is reserved.",
                'variables' => ['client_name', 'property_title', 'property_address', 'viewing_time', 'agent_name'],
                'rules' => [
                    'requires_time_slot' => 'true',
                    'include_access_instructions' => 'true',
                ],
                'is_active' => true,
            ],

            // =========================================================================
            // 2. COMPLIANCE & LEGAL NOTICES (Category ID 7 - Channel: EMAIL)
            // =========================================================================
            [
                'id' => 3,
                'category_id' => 7, // Compliance & Legal Notices
                'name' => 'Fair Housing & Regulatory Disclosure Notice',
                'channel' => ThreadChannel::EMAIL,
                'system_instructions' => 'You are an executive compliance counsel. Use precise, legally certified real estate terminology adhering strictly to Fair Housing Act guidelines with zero discriminatory language.',
                'prompt' => "Generate the official regulatory disclosure and representation disclosure to {{client_name}} regarding {{property_title}}.\n\nState clearly that MatchMaker operates under Equal Housing Opportunity standards, reference Broker License #{{broker_license}}, and attach the statutory advisory disclosure.",
                'variables' => ['client_name', 'property_title', 'broker_license'],
                'rules' => [
                    'compliance_gate' => 'mandatory',
                    'immutable_text' => 'true',
                ],
                'is_active' => true,
            ],
            [
                'id' => 4,
                'category_id' => 7,
                'name' => 'Formal Price Negotiation & Counter-Offer Proposal',
                'channel' => ThreadChannel::EMAIL,
                'system_instructions' => 'You are an experienced broker manager. Maintain strong negotiating posture while remaining diplomatic and solution-oriented.',
                'prompt' => "Draft a formal price negotiation letter to {{client_name}} regarding their offer on {{property_title}}.\n\n* Acknowledge their proposed offer of {{submitted_offer}}.\n* Respectfully present the seller's counter-offer of {{counter_offer}}.\n* Detail the included terms: {{included_terms}} and set an acceptance validity window of {{validity_hours}} hours.",
                'variables' => ['client_name', 'property_title', 'submitted_offer', 'counter_offer', 'included_terms', 'validity_hours'],
                'rules' => [
                    'requires_notary_clause' => 'true',
                    'expiry_window' => '48_hours',
                ],
                'is_active' => true,
            ],
            [
                'id' => 5,
                'category_id' => 7,
                'name' => 'Proof of Funds & Escrow Pre-Qualification',
                'channel' => ThreadChannel::EMAIL,
                'system_instructions' => 'You are a private banking liaison. Request financial verification with utmost discretion and confidentiality.',
                'prompt' => "Draft a confidential request to {{client_name}} detailing the financial verification requirements for {{property_title}}.\n\nExplain that before presenting a binding acquisition contract to the seller, a bank verification letter confirming funds or an escrow deposit of {{escrow_amount}} is required.",
                'variables' => ['client_name', 'property_title', 'escrow_amount', 'agent_name'],
                'rules' => [
                    'banking_verification' => 'mandatory',
                    'confidentiality_level' => 'maximum',
                ],
                'is_active' => true,
            ],

            // =========================================================================
            // 3. WHATSAPP INSTANT RESPONSES (Channel: WHATSAPP)
            // =========================================================================
            [
                'id' => 6,
                'category_id' => 6,
                'name' => 'WhatsApp Fast Brochure & Pricing Dispatch',
                'channel' => ThreadChannel::WHATSAPP,
                'system_instructions' => 'You are a responsive WhatsApp concierge. Keep text strictly under 80 words, use tasteful formatting, and provide direct actionable links.',
                'prompt' => "Send a rapid WhatsApp message to {{client_name}}:\n\n'Hello {{client_name}}, thank you for inquiring about *{{property_title}}* in {{property_city}} ({{property_price}}). Here is your exclusive PDF brochure: {{brochure_url}}. When would you like to schedule a private tour with {{agent_name}}?'",
                'variables' => ['client_name', 'property_title', 'property_city', 'property_price', 'brochure_url', 'agent_name'],
                'rules' => [
                    'format' => 'whatsapp_markdown',
                    'max_words' => '80',
                ],
                'is_active' => true,
            ],
            [
                'id' => 7,
                'category_id' => 6,
                'name' => 'WhatsApp Viewing Reschedule Request',
                'channel' => ThreadChannel::WHATSAPP,
                'system_instructions' => 'Be concise, polite, and offer two clear alternative time options.',
                'prompt' => "Send a quick WhatsApp message to {{client_name}} regarding their appointment for {{property_title}}.\n\nPropose moving the tour to either {{option_1_time}} or {{option_2_time}} and ask which works best.",
                'variables' => ['client_name', 'property_title', 'option_1_time', 'option_2_time'],
                'rules' => [
                    'format' => 'whatsapp_markdown',
                    'urgent' => 'true',
                ],
                'is_active' => true,
            ],

            // =========================================================================
            // 4. WEBFORM AUTOMATIONS (Channel: WEBFORM)
            // =========================================================================
            [
                'id' => 8,
                'category_id' => 6,
                'name' => 'Webform Instant Lead Auto-Acknowledgment',
                'channel' => ThreadChannel::WEBFORM,
                'system_instructions' => 'Automated luxury CRM response engine. Reassure the client that their inquiry is being reviewed by a dedicated private client advisor.',
                'prompt' => "Generate an automated web confirmation for {{client_name}} thanking them for registering on the MatchMaker luxury portal regarding {{inquiry_topic}}.\n\nState that senior agent {{agent_name}} will review their criteria and send tailored options within 2 hours.",
                'variables' => ['client_name', 'inquiry_topic', 'agent_name'],
                'rules' => [
                    'auto_dispatch' => 'true',
                ],
                'is_active' => true,
            ],

            // =========================================================================
            // 5. GLOBAL GENERAL TEMPLATES (category_id: null)
            // =========================================================================
            [
                'id' => 9,
                'category_id' => null, // Global Scope
                'name' => 'Stalled Deal Check-In & Price Adjustment Alert',
                'channel' => ThreadChannel::EMAIL,
                'system_instructions' => 'You are a senior relationship manager re-engaging a warm client. Focus on newly updated price points and market positioning.',
                'prompt' => "Draft an engaging check-in email to {{client_name}} regarding {{property_title}}.\n\n* Note that the seller has newly adjusted the asking price to {{new_price}}.\n* Inquire if they would like to review the updated financial deck or take a second look this weekend.",
                'variables' => ['client_name', 'property_title', 'new_price', 'agent_name'],
                'rules' => [
                    're_engagement' => 'true',
                ],
                'is_active' => true,
            ],
            [
                'id' => 10,
                'category_id' => null, // Global Scope
                'name' => 'Commercial Grade-A Lease Terms Summary',
                'channel' => ThreadChannel::EMAIL,
                'system_instructions' => 'You are a corporate commercial leasing director. Detail lease durations, fit-out periods, and utility infrastructure.',
                'prompt' => "Draft a commercial lease executive summary for {{contact_person}} at {{company_name}} regarding {{property_title}} ({{sqm_area}} sqm).\n\nOutline the {{lease_years}}-year lease term, {{fit_out_days}} days rent-free fit-out period, and dedicated parking allocations.",
                'variables' => ['contact_person', 'company_name', 'property_title', 'sqm_area', 'lease_years', 'fit_out_days'],
                'rules' => [
                    'commercial_grade' => 'A',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $templateData) {
            Template::updateOrCreate(
                ['id' => $templateData['id']],
                $templateData
            );
        }
    }
}
