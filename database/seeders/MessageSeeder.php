<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Message\MessageDirection;
use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        Message::withoutEvents(function () {
            $messages = [
                // =========================================================================
                // THREAD 1: Sultan Al-Otaibi — Hydra Diplomatic Estate (Thread ID: 1)
                // =========================================================================
                [
                    'id' => 1,
                    'thread_id' => 1,
                    'client_id' => 1,
                    'user_id' => null,
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_001_sultan_inbound@mail.matchmaker.test',
                    'in_reply_to' => null,
                    'direction' => MessageDirection::INBOUND,
                    'body_text' => "Hello MatchMaker Team,\n\nI am reviewing the Grand Hydra Estate ($4.8M) in Chemin Doudou Mokhtar. We require a 6-bedroom ambassadorial compound with reinforced perimeter security, heated indoor/outdoor pool, and independent quarters for our personal security detail.\n\nCan you confirm if the property holds immediate diplomatic zoning clearance?",
                    'body_html' => "<p>Hello MatchMaker Team,</p><p>I am reviewing the <strong>Grand Hydra Estate ($4.8M)</strong> in Chemin Doudou Mokhtar. We require a 6-bedroom ambassadorial compound with reinforced perimeter security, heated pool, and independent quarters for our security detail.</p><p>Can you confirm if the property holds immediate diplomatic zoning clearance?</p><p>Best regards,<br><strong>Sultan Al-Otaibi</strong><br>Gulf Capital Investments</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => null,
                    'created_at' => now()->subHours(3),
                ],
                [
                    'id' => 2,
                    'thread_id' => 1,
                    'client_id' => null,
                    'user_id' => 4, // Agent Demo
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_002_agent_outbound@mail.matchmaker.test',
                    'in_reply_to' => 'msg_001_sultan_inbound@mail.matchmaker.test',
                    'direction' => MessageDirection::OUTBOUND,
                    'body_text' => "Dear Mr. Al-Otaibi,\n\nThank you for reaching out. Yes, the Grand Hydra Estate is fully certified for diplomatic mission use with biometric gates, dual power generators, and independent 120 sqm staff quarters.\n\nI have attached the private cadastral memorandum for your review.",
                    'body_html' => "<p>Dear Mr. Al-Otaibi,</p><p>Thank you for reaching out. Yes, the <strong>Grand Hydra Estate</strong> is fully certified for diplomatic mission use with biometric access, dual-redundant power generators, and independent 120 sqm staff quarters.</p><p>I have attached the private architectural memorandum for your legal counsel's review.</p><p>Warm regards,<br><strong>Agent Demo</strong><br>Senior Private Client Advisor | MatchMaker</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => [
                        ['name' => 'Hydra_Estate_Cadastral_Memorandum.pdf', 'url' => '#'],
                    ],
                    'created_at' => now()->subHours(1),
                ],
                [
                    'id' => 3,
                    'thread_id' => 1,
                    'client_id' => 1,
                    'user_id' => null,
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_003_sultan_reply@mail.matchmaker.test',
                    'in_reply_to' => 'msg_002_agent_outbound@mail.matchmaker.test',
                    'direction' => MessageDirection::INBOUND,
                    'body_text' => "Excellent. I will be in Algiers this Friday. Can we arrange an on-site private inspection at 10:30 AM with my architectural consultant?",
                    'body_html' => "<p>Excellent. I will be in Algiers this Friday morning. Can we arrange an on-site private inspection at <strong>10:30 AM</strong> with my architectural consultant?</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => null,
                    'created_at' => now()->subMinutes(15),
                ],
                // PENDING AI DRAFT (Visible in Copilot Panel!)
                [
                    'id' => 4,
                    'thread_id' => 1,
                    'client_id' => null,
                    'user_id' => 4,
                    'template_id' => 2, // Viewing Confirmation Template
                    'mailbox_message_id' => null,
                    'in_reply_to' => 'msg_003_sultan_reply@mail.matchmaker.test',
                    'direction' => MessageDirection::OUTBOUND,
                    'body_text' => "Dear Mr. Al-Otaibi,\n\nWe are pleased to confirm your private viewing of The Grand Hydra Estate for this Friday at 10:30 AM. Security clearance for you and your architectural consultant has been arranged at the main gate.",
                    'body_html' => "<p>Dear Mr. Al-Otaibi,</p><p>We are pleased to confirm your exclusive private inspection of <strong>The Grand Hydra Estate</strong> for this <strong>Friday at 10:30 AM</strong>.</p><p>Security protocol clearance for you and your architectural consultant has been submitted to the estate concierge. Private valet parking will be reserved at the main gate on Chemin Doudou Mokhtar.</p><p>Should you require executive airport transfer upon your arrival, please let us know.</p><p>Sincerely,<br><strong>Agent Demo</strong><br>MatchMaker Private Office</p>",
                    'is_draft' => true,
                    'is_ai_generated' => true,
                    'attachments' => null,
                    'created_at' => now()->subMinutes(5),
                ],

                // =========================================================================
                // THREAD 2: Noura Al-Mansoor — Canastel Cliffside Villa (Thread ID: 2)
                // =========================================================================
                [
                    'id' => 5,
                    'thread_id' => 2,
                    'client_id' => 2,
                    'user_id' => null,
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_005_noura_inbound@mail.matchmaker.test',
                    'in_reply_to' => null,
                    'direction' => MessageDirection::INBOUND,
                    'body_text' => "Hello, I am inquiring regarding the panoramic cliffside villa in Canastel, Oran ($4,500/mo). We are looking for a 12-month lease starting June. Does the rental include weekly pool maintenance and fiber optic internet?",
                    'body_html' => "<p>Hello,</p><p>I am inquiring regarding the <strong>panoramic cliffside villa in Canastel, Oran ($4,500/mo)</strong>. We are looking for a 12-month lease starting June. Does the rental include weekly pool maintenance and high-speed fiber internet?</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => null,
                    'created_at' => now()->subHours(4),
                ],
                [
                    'id' => 6,
                    'thread_id' => 2,
                    'client_id' => null,
                    'user_id' => 4,
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_006_agent_reply@mail.matchmaker.test',
                    'in_reply_to' => 'msg_005_noura_inbound@mail.matchmaker.test',
                    'direction' => MessageDirection::OUTBOUND,
                    'body_text' => "Dear Ms. Al-Mansoor, Yes, the lease includes twice-weekly pool servicing, gardener, and high-speed fiber connectivity. The villa is fully designer-furnished.",
                    'body_html' => "<p>Dear Ms. Al-Mansoor,</p><p>Yes, the long-term lease includes twice-weekly pool servicing, dedicated groundskeeping, and high-speed fiber connectivity. The villa is furnished with Italian contemporary interiors.</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => null,
                    'created_at' => now()->subHours(2),
                ],
                // PENDING AI DRAFT
                [
                    'id' => 7,
                    'thread_id' => 2,
                    'client_id' => null,
                    'user_id' => 4,
                    'template_id' => 1,
                    'mailbox_message_id' => null,
                    'in_reply_to' => 'msg_005_noura_inbound@mail.matchmaker.test',
                    'direction' => MessageDirection::OUTBOUND,
                    'body_text' => "Hello Noura, would you like to review the 3D virtual walkthrough of the Canastel villa or schedule an in-person viewing?",
                    'body_html' => "<p>Hello Noura,</p><p>We can arrange a live 3D virtual walkthrough of the <strong>Canastel Cliffside Villa</strong> or schedule an in-person tour whenever you visit Oran.</p><p>Please let us know your preferred dates.</p>",
                    'is_draft' => true,
                    'is_ai_generated' => true,
                    'attachments' => null,
                    'created_at' => now()->subMinutes(30),
                ],

                // =========================================================================
                // THREAD 3: Alexander Vance — Bab Ezzouar Corporate Floor (Thread ID: 3)
                // =========================================================================
                [
                    'id' => 8,
                    'thread_id' => 3,
                    'client_id' => 3,
                    'user_id' => null,
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_008_vance_inbound@mail.matchmaker.test',
                    'in_reply_to' => null,
                    'direction' => MessageDirection::INBOUND,
                    'body_text' => "Good day, TechVentures is evaluating the 750 sqm Grade-A office floor in Bab Ezzouar ($12,000/mo). What is the landlord's policy on fit-out rent-free periods for a 5-year lease?",
                    'body_html' => "<p>Good day,</p><p>TechVentures is evaluating the <strong>750 sqm Grade-A office floor in Bab Ezzouar ($12,000/mo)</strong>. What is the landlord's policy regarding fit-out rent-free periods for a 5-year commercial lease?</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => null,
                    'created_at' => now()->subHours(6),
                ],
                // PENDING AI DRAFT
                [
                    'id' => 9,
                    'thread_id' => 3,
                    'client_id' => null,
                    'user_id' => 4,
                    'template_id' => 10,
                    'mailbox_message_id' => null,
                    'in_reply_to' => 'msg_008_vance_inbound@mail.matchmaker.test',
                    'direction' => MessageDirection::OUTBOUND,
                    'body_text' => "Dear Mr. Vance, The landlord is offering a 90-day rent-free fit-out period for 5-year corporate commitments, including 15 dedicated parking spaces.",
                    'body_html' => "<p>Dear Mr. Vance,</p><p>For a 5-year corporate commitment on the <strong>Bab Ezzouar Headquarters Floor</strong>, the landlord provides a <strong>90-day rent-free fit-out period</strong>, along with 15 dedicated underground parking allocations and dual-generator power backup.</p><p>We can deliver the draft commercial lease heads of terms for your legal counsel today.</p>",
                    'is_draft' => true,
                    'is_ai_generated' => true,
                    'attachments' => null,
                    'created_at' => now()->subHours(1),
                ],

                // =========================================================================
                // THREAD 4: Layla Al-Khatib — Paris Avenue Montaigne Penthouse (Thread ID: 4)
                // =========================================================================
                [
                    'id' => 10,
                    'thread_id' => 4,
                    'client_id' => 4,
                    'user_id' => null,
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_010_layla_inbound@mail.matchmaker.test',
                    'in_reply_to' => null,
                    'direction' => MessageDirection::INBOUND,
                    'body_text' => "Dear MatchMaker, I am reviewing the Avenue Montaigne Penthouse (€3.6M). We would like to verify the co-ownership charges and whether notary deed signing can be coordinated remotely.",
                    'body_html' => "<p>Dear MatchMaker,</p><p>I am reviewing the <strong>Avenue Montaigne Penthouse (€3.6M)</strong>. We would like to verify the quarterly co-ownership charges and whether notary deed signing can be coordinated via international power of attorney.</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => null,
                    'created_at' => now()->subDays(2),
                ],
                [
                    'id' => 11,
                    'thread_id' => 4,
                    'client_id' => null,
                    'user_id' => 4,
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_011_agent_outbound@mail.matchmaker.test',
                    'in_reply_to' => 'msg_010_layla_inbound@mail.matchmaker.test',
                    'direction' => MessageDirection::OUTBOUND,
                    'body_text' => "Dear Ms. Al-Khatib, Quarterly charges are €2,400 including full concierge and security. Remote notary signing via French consular procuration is fully supported.",
                    'body_html' => "<p>Dear Ms. Al-Khatib,</p><p>Quarterly charges are €2,400 including full concierge and lift maintenance. Remote notary signing via French consular procuration is fully supported by our partner notaries in Paris.</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => null,
                    'created_at' => now()->subDay(),
                ],
                // PENDING AI DRAFT
                [
                    'id' => 12,
                    'thread_id' => 4,
                    'client_id' => null,
                    'user_id' => 4,
                    'template_id' => 5,
                    'mailbox_message_id' => null,
                    'in_reply_to' => 'msg_010_layla_inbound@mail.matchmaker.test',
                    'direction' => MessageDirection::OUTBOUND,
                    'body_text' => "Dear Ms. Al-Khatib, Would you like us to submit a preliminary acquisition proposal on your behalf to secure exclusivity?",
                    'body_html' => "<p>Dear Ms. Al-Khatib,</p><p>Would you like our private office to draft a preliminary letter of intent (Offre d'Achat) on your behalf to secure exclusivity on the <strong>Avenue Montaigne Penthouse</strong> before the upcoming public viewing cycle?</p>",
                    'is_draft' => true,
                    'is_ai_generated' => true,
                    'attachments' => null,
                    'created_at' => now()->subHours(4),
                ],

                // =========================================================================
                // THREAD 5: Jonathan Sterling — Miami Brickell Sky Penthouse (Thread ID: 5)
                // =========================================================================
                [
                    'id' => 13,
                    'thread_id' => 5,
                    'client_id' => 5,
                    'user_id' => null,
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_013_sterling_inbound@mail.matchmaker.test',
                    'in_reply_to' => null,
                    'direction' => MessageDirection::INBOUND,
                    'body_text' => "We are ready to wire the 10% escrow deposit for the Brickell Avenue Sky Penthouse ($2.85M). Please send escrow wire instructions.",
                    'body_html' => "<p>We are prepared to wire the 10% earnest escrow deposit for the <strong>Brickell Avenue Sky Penthouse ($2.85M)</strong>. Please provide the closing title attorney's wire instructions.</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => null,
                    'created_at' => now()->subDays(3),
                ],
                [
                    'id' => 14,
                    'thread_id' => 5,
                    'client_id' => null,
                    'user_id' => 4,
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_014_agent_outbound@mail.matchmaker.test',
                    'in_reply_to' => 'msg_013_sterling_inbound@mail.matchmaker.test',
                    'direction' => MessageDirection::OUTBOUND,
                    'body_text' => "Wire instructions transmitted under separate encrypted cover. Thread snoozed pending escrow receipt.",
                    'body_html' => "<p>Wire instructions transmitted under separate encrypted cover. Thread snoozed pending escrow verification.</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => null,
                    'created_at' => now()->subDays(2),
                ],

                // =========================================================================
                // THREAD 6: Camille Dubois — Cap de Nice Waterfront Villa (Thread ID: 6 - CLOSED)
                // =========================================================================
                [
                    'id' => 15,
                    'thread_id' => 6,
                    'client_id' => 6,
                    'user_id' => null,
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_015_camille_inbound@mail.matchmaker.test',
                    'in_reply_to' => null,
                    'direction' => MessageDirection::INBOUND,
                    'body_text' => "The maritime mooring inspection on the Cap de Nice Villa was completely satisfactory. We have executed the binding compromis de vente.",
                    'body_html' => "<p>The maritime mooring inspection on the <strong>Cap de Nice Villa (€5.9M)</strong> was completely satisfactory. We have signed the binding compromis de vente with Notaires de Nice.</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => null,
                    'created_at' => now()->subDays(6),
                ],
                [
                    'id' => 16,
                    'thread_id' => 6,
                    'client_id' => null,
                    'user_id' => 4,
                    'template_id' => null,
                    'mailbox_message_id' => 'msg_016_agent_outbound@mail.matchmaker.test',
                    'in_reply_to' => 'msg_015_camille_inbound@mail.matchmaker.test',
                    'direction' => MessageDirection::OUTBOUND,
                    'body_text' => "Congratulations Camille. Transaction marked as Under Offer. File successfully archived.",
                    'body_html' => "<p>Congratulations Camille. The transaction has been updated to <em>Under Offer</em> and the file is archived.</p>",
                    'is_draft' => false,
                    'is_ai_generated' => false,
                    'attachments' => null,
                    'created_at' => now()->subDays(5),
                ],
            ];

            foreach ($messages as $messageData) {
                Message::updateOrCreate(
                    ['id' => $messageData['id']],
                    $messageData
                );
            }
        });
    }
}
