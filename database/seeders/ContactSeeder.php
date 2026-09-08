<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ContactMessageStatus;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            // =========================================================================
            // 1. UNREAD INCOMING INQUIRIES (IDs 1 - 5) — Triggers Red Sidebar Badge
            // =========================================================================
            [
                'id' => 1,
                'name' => 'Karim Benali',
                'email' => 'karim.benali@diaspora-invest.test',
                'phone' => '+213 550 44 88 12',
                'subject' => 'Diplomatic Villa in Hydra — Private Viewing Request',
                'message' => "Hello MatchMaker Team,\n\nI am currently based in Paris and looking to acquire a primary family compound in **Hydra, Algiers**. The *Grand Hydra Estate* listing matches our exact security and architectural criteria.\n\nCould we schedule a private on-site tour for next Friday morning? I will be in Algiers with my legal advisor.\n\nBest regards,\n**Karim Benali**",
                'ip_address' => '197.200.45.12', // Algiers Telecom IP
                'status' => ContactMessageStatus::Unread,
                'created_at' => now()->subHours(2),
            ],
            [
                'id' => 2,
                'name' => 'Camille Dubois',
                'email' => 'camille.dubois@luxury-paris.test',
                'phone' => '+33 6 45 89 22 10',
                'subject' => 'Avenue Montaigne Penthouse Acquisition Details',
                'message' => "Dear Agent,\n\nI am reaching out regarding the *Haussmannian Penthouse on Avenue Montaigne* (€3.6M). We would like to confirm whether the private elevator landing and the cellar are included in the freehold title.\n\nPlease share the confidential sales memorandum and DPE energy diagnostics at your earliest convenience.",
                'ip_address' => '82.64.120.15', // Paris Orange IP
                'status' => ContactMessageStatus::Unread,
                'created_at' => now()->subHours(5),
            ],
            [
                'id' => 3,
                'name' => 'Yacine Mansouri',
                'email' => 'yacine.m@techholding-dz.test',
                'phone' => '+213 661 23 45 67',
                'subject' => 'Bab Ezzouar Headquarters Lease — 750 sqm Floor',
                'message' => "Good day,\n\nOur technology group is expanding our regional operational team and looking to lease the 750 sqm Grade-A floor in the **Bab Ezzouar Business Park**.\n\nWe require high-bandwidth fiber connectivity and 15 parking bays. What is the earliest availability date for tenant fit-out?",
                'ip_address' => '105.101.88.34', // Algiers Ooredoo IP
                'status' => ContactMessageStatus::Unread,
                'created_at' => now()->subHours(12),
            ],
            [
                'id' => 4,
                'name' => 'Jonathan Sterling',
                'email' => 'jsterling@brickellcapital.test',
                'phone' => '+1 (305) 555-8910',
                'subject' => 'Miami Sky Penthouse — Cash Acquisition Offer',
                'message' => "Hi there,\n\nI am reviewing the *1421 Brickell Avenue Penthouse* listing ($2.85M). My equity group is prepared to submit an all-cash offer with an expedited 14-day escrow closing period.\n\nPlease connect me with the listing broker directly.",
                'ip_address' => '172.56.21.90', // Miami T-Mobile IP
                'status' => ContactMessageStatus::Unread,
                'created_at' => now()->subDay(),
            ],
            [
                'id' => 5,
                'name' => 'Amina Cherif',
                'email' => 'amina.cherif@consulting.test',
                'phone' => '+213 770 99 11 22',
                'subject' => 'Val d\'Hydra Duplex — Payment Terms Inquiry',
                'message' => "Hello,\n\nI am interested in the *Modern Duplex in El Biar* ($1.25M). Is the seller open to staged notary payments over a 6-month escrow milestone schedule?\n\nLooking forward to your guidance.",
                'ip_address' => '41.107.12.8', // Djezzy Algeria IP
                'status' => ContactMessageStatus::Unread,
                'created_at' => now()->subDays(2),
            ],

            // =========================================================================
            // 2. READ / REVIEWED INQUIRIES (IDs 6 - 10)
            // =========================================================================
            [
                'id' => 6,
                'name' => 'Jean-Luc Moreau',
                'email' => 'jl.moreau@riviera-estates.test',
                'phone' => '+33 4 93 11 22 33',
                'subject' => 'Cap de Nice Sea-Front Villa — Mooring Verification',
                'message' => "Bonjour,\n\nRegarding the *Cap de Nice Waterfront Villa* (€5.9M), does the private boat slip hold maritime authorization for vessels up to 14 meters?\n\nThank you for checking with the seller.",
                'ip_address' => '90.84.144.60', // Nice SFR IP
                'status' => ContactMessageStatus::Read,
                'created_at' => now()->subDays(3),
            ],
            [
                'id' => 7,
                'name' => 'Tarek Brahimi',
                'email' => 'tarek.brahimi@oran-holding.test',
                'phone' => '+213 551 77 66 55',
                'subject' => 'Canastel Panoramic Villa — Long-Term Summer Lease',
                'message' => "Hello,\n\nWe would like to book a 12-month corporate residential lease for the cliffside villa in Canastel ($4,500/mo). We require furnished inventory and pool maintenance included.",
                'ip_address' => '105.108.20.14', // Oran Telecom IP
                'status' => ContactMessageStatus::Read,
                'created_at' => now()->subDays(4),
            ],
            [
                'id' => 8,
                'name' => 'Rachel Greenbaum',
                'email' => 'rachel.g@sohodesign.test',
                'phone' => '+1 (212) 555-0142',
                'subject' => 'SoHo Cast-Iron Loft — Live/Work Zoning',
                'message' => "Hi Agent Demo,\n\nWe are looking to lease the *Spring Street Cast-Iron Loft* in SoHo ($16,500/mo) for an architectural design studio. Can you confirm if JLQA live/work variance permits 6 staff members on site?",
                'ip_address' => '68.195.80.22', // NYC Verizon IP
                'status' => ContactMessageStatus::Read,
                'created_at' => now()->subDays(5),
            ],
            [
                'id' => 9,
                'name' => 'Samir Hadj-Ali',
                'email' => 'samir.hadjali@commercial-dz.test',
                'phone' => '+213 660 33 22 11',
                'subject' => 'Ain El Turk Development Parcel — Cadastral Survey',
                'message' => "Greetings,\n\nWe are evaluating the 2,500 sqm beachfront parcel in Ain El Turk for a boutique hotel development. Please provide the official cadastral certificate (Livret Foncier) and topographical survey.",
                'ip_address' => '197.206.110.5', // Oran Mobilis IP
                'status' => ContactMessageStatus::Read,
                'created_at' => now()->subDays(6),
            ],
            [
                'id' => 10,
                'name' => 'Elena Rostova',
                'email' => 'elena.audit@realty-eu.test',
                'phone' => '+33 7 50 12 34 56',
                'subject' => 'MatchMaker CRM Enterprise Demo & API Documentation',
                'message' => "Hello,\n\nOur brokerage network is evaluating your AI Shared Inbox platform for 45 agents. We would like to request an API integration briefing regarding your Google Gemini compliance models and Postmark webhook endpoints.",
                'ip_address' => '176.31.220.88', // Lyon Free SAS IP
                'status' => ContactMessageStatus::Read,
                'created_at' => now()->subDays(7),
            ],

            // =========================================================================
            // 3. RESOLVED / COMPLETED INQUIRIES (IDs 11 - 15)
            // =========================================================================
            [
                'id' => 11,
                'name' => 'Farid Khelil',
                'email' => 'farid.khelil@energy-invest.test',
                'phone' => '+213 550 88 77 66',
                'subject' => 'Akid Lotfi Luxury Apartment — Notary Contract Finalized',
                'message' => "Thank you for facilitating the notary transfer for the Akid Lotfi apartment. All keys and deeds have been delivered to our representative.",
                'ip_address' => '105.106.90.4',
                'status' => ContactMessageStatus::Resolved,
                'created_at' => now()->subWeeks(2),
            ],
            [
                'id' => 12,
                'name' => 'Béatrice Fontaine',
                'email' => 'b.fontaine@paris-notaires.test',
                'phone' => '+33 1 42 68 55 00',
                'subject' => 'Avenue Montaigne Escrow Deposit Confirmation',
                'message' => "Dear MatchMaker Team,\n\nThis is to confirm that the preliminary escrow deposit for the Avenue Montaigne property has cleared our trust account successfully.",
                'ip_address' => '195.154.122.9',
                'status' => ContactMessageStatus::Resolved,
                'created_at' => now()->subWeeks(2),
            ],
            [
                'id' => 13,
                'name' => 'Michael Chang',
                'email' => 'mchang@pacific-ventures.test',
                'phone' => '+1 (415) 555-3211',
                'subject' => 'Enterprise Agency Subscription Onboarding Completed',
                'message' => "The dedicated Postmark server integration and customized FHA compliance rules are live. Thank you for the white-glove onboarding.",
                'ip_address' => '24.130.40.18',
                'status' => ContactMessageStatus::Resolved,
                'created_at' => now()->subWeeks(3),
            ],
            [
                'id' => 14,
                'name' => 'Zoubida Belkacem',
                'email' => 'zoubida.belkacem@algiers-med.test',
                'phone' => '+213 771 55 44 33',
                'subject' => 'Hydra Residential Lease Agreement Signed',
                'message' => "The residential tenancy agreement for our executive staff has been signed and registered at the Hydra municipality. Thank you for the prompt assistance.",
                'ip_address' => '41.102.30.70',
                'status' => ContactMessageStatus::Resolved,
                'created_at' => now()->subWeeks(3),
            ],
            [
                'id' => 15,
                'name' => 'Alexandre Marchand',
                'email' => 'a.marchand@cannes-yachting.test',
                'phone' => '+33 6 88 99 00 11',
                'subject' => 'Nice Waterfront Mooring Authorization Delivered',
                'message' => "The port authority mooring certificate for the Cap de Nice property has been validated. File closed.",
                'ip_address' => '80.12.55.90',
                'status' => ContactMessageStatus::Resolved,
                'created_at' => now()->subMonth(),
            ],
        ];

        foreach ($contacts as $contactData) {
            Contact::updateOrCreate(
                ['id' => $contactData['id']],
                $contactData
            );
        }
    }
}
