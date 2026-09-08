<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Thread\ThreadChannel;
use App\Enums\Thread\ThreadPriority;
use App\Enums\Thread\ThreadStatus;
use App\Models\Thread;
use Illuminate\Database\Seeder;

class ThreadSeeder extends Seeder
{
    public function run(): void
    {
        $threads = [
            // =========================================================================
            // 1. HYDRA DIPLOMATIC VILLA INQUIRY (Sultan Al-Otaibi -> Matches Property 1)
            // =========================================================================
            [
                'id' => 1,
                'client_id' => 1, // Sultan Al-Otaibi
                'assigned_user_id' => 4, // Agent Demo
                'mailbox_hash' => 'mbx_hydra_villa_sultan_01',
                'subject' => 'Inquiry: 6-Bedroom Diplomatic Estate in Hydra (Grand Hydra Estate)',
                'status' => ThreadStatus::OPEN,
                'priority' => ThreadPriority::HIGH,
                'channel' => ThreadChannel::EMAIL,
                'is_unread' => true, // Shows unread badge in Inbox UI
                'snoozed_until' => null,
                'extracted_criteria' => [
                    'is_property_inquiry' => true,
                    'listing_type' => 'sale',
                    'property_type' => 'villa',
                    'city' => 'Algiers',
                    'budget_max' => 5_000_000,
                    'bedrooms' => 6,
                    'preferred_district' => 'Hydra',
                    'amenities' => ['swimming pool', 'high security', 'ambassadorial layout'],
                ],
                'last_message_at' => now()->subMinutes(15),
            ],

            // =========================================================================
            // 2. ORAN CANASTEL SEASIDE LEASE (Noura Al-Mansoor -> Matches Property 4)
            // =========================================================================
            [
                'id' => 2,
                'client_id' => 2, // Noura Al-Mansoor
                'assigned_user_id' => 4, // Agent Demo
                'mailbox_hash' => 'mbx_canastel_villa_noura_02',
                'subject' => 'Summer Lease: Panoramic Sea-View Villa in Canastel',
                'status' => ThreadStatus::OPEN,
                'priority' => ThreadPriority::HIGH,
                'channel' => ThreadChannel::WHATSAPP,
                'is_unread' => false,
                'snoozed_until' => null,
                'extracted_criteria' => [
                    'is_property_inquiry' => true,
                    'listing_type' => 'rent',
                    'property_type' => 'villa',
                    'city' => 'Oran',
                    'budget_max' => 5_000,
                    'bedrooms' => 4,
                    'preferred_district' => 'Canastel',
                    'amenities' => ['infinity pool', 'panoramic sea view', 'furnished'],
                ],
                'last_message_at' => now()->subHours(2),
            ],

            // =========================================================================
            // 3. BAB EZZOUAR CORPORATE LEASE (Alexander Vance -> Matches Property 3)
            // =========================================================================
            [
                'id' => 3,
                'client_id' => 3, // Alexander Vance
                'assigned_user_id' => 4, // Agent Demo
                'mailbox_hash' => 'mbx_bab_ezzouar_vance_03',
                'subject' => 'Commercial Lease: 750 sqm Office Floor in Bab Ezzouar',
                'status' => ThreadStatus::NEW,
                'priority' => ThreadPriority::URGENT,
                'channel' => ThreadChannel::EMAIL,
                'is_unread' => true, // Shows unread badge in Inbox UI
                'snoozed_until' => null,
                'extracted_criteria' => [
                    'is_property_inquiry' => true,
                    'listing_type' => 'rent',
                    'property_type' => 'commercial',
                    'city' => 'Algiers',
                    'budget_max' => 15_000,
                    'bedrooms' => 0,
                    'preferred_district' => 'Bab Ezzouar',
                    'amenities' => ['fiber optic', '15 parking spots', 'corporate headquarters'],
                ],
                'last_message_at' => now()->subHours(6),
            ],

            // =========================================================================
            // 4. PARIS AVENUE MONTAIGNE PENTHOUSE (Layla Al-Khatib -> Matches Property 7)
            // =========================================================================
            [
                'id' => 4,
                'client_id' => 4, // Layla Al-Khatib
                'assigned_user_id' => 4, // Agent Demo
                'mailbox_hash' => 'mbx_paris_penthouse_layla_04',
                'subject' => 'Avenue Montaigne Penthouse Acquisition Consultation',
                'status' => ThreadStatus::OPEN,
                'priority' => ThreadPriority::NORMAL,
                'channel' => ThreadChannel::EMAIL,
                'is_unread' => false,
                'snoozed_until' => null,
                'extracted_criteria' => [
                    'is_property_inquiry' => true,
                    'listing_type' => 'sale',
                    'property_type' => 'apartment',
                    'city' => 'Paris',
                    'budget_max' => 4_000_000,
                    'bedrooms' => 4,
                    'preferred_district' => '8th Arrondissement',
                    'amenities' => ['Eiffel Tower view', 'Haussmannian parquet', 'private landing'],
                ],
                'last_message_at' => now()->subDay(),
            ],

            // =========================================================================
            // 5. MIAMI BRICKELL PENTHOUSE (Jonathan Sterling -> Matches Property 9)
            // =========================================================================
            [
                'id' => 5,
                'client_id' => 5, // Jonathan Sterling
                'assigned_user_id' => 4, // Agent Demo
                'mailbox_hash' => 'mbx_miami_penthouse_sterling_05',
                'subject' => 'Brickell Sky Penthouse 54th Floor — Pre-Offer Verification',
                'status' => ThreadStatus::SNOOZED,
                'priority' => ThreadPriority::NORMAL,
                'channel' => ThreadChannel::WEBFORM,
                'is_unread' => false,
                'snoozed_until' => now()->addDays(2),
                'extracted_criteria' => [
                    'is_property_inquiry' => true,
                    'listing_type' => 'sale',
                    'property_type' => 'apartment',
                    'city' => 'Miami',
                    'budget_max' => 3_000_000,
                    'bedrooms' => 3,
                    'preferred_district' => 'Brickell Avenue',
                    'amenities' => ['bay view', 'rooftop pool', 'valet'],
                ],
                'last_message_at' => now()->subDays(2),
            ],

            // =========================================================================
            // 6. NICE CAP DE NICE WATERFRONT (Camille Dubois -> Matches Property 8)
            // =========================================================================
            [
                'id' => 6,
                'client_id' => 6, // Camille Dubois
                'assigned_user_id' => 4, // Agent Demo
                'mailbox_hash' => 'mbx_nice_villa_dubois_06',
                'subject' => 'Cap de Nice Waterfront Villa Tour & Mooring Review',
                'status' => ThreadStatus::CLOSED,
                'priority' => ThreadPriority::NORMAL,
                'channel' => ThreadChannel::EMAIL,
                'is_unread' => false,
                'snoozed_until' => null,
                'extracted_criteria' => [
                    'is_property_inquiry' => true,
                    'listing_type' => 'sale',
                    'property_type' => 'villa',
                    'city' => 'Nice',
                    'budget_max' => 6_500_000,
                    'bedrooms' => 5,
                    'preferred_district' => 'Cap de Nice',
                    'amenities' => ['private boat slip', 'infinity pool', 'Villefranche bay view'],
                ],
                'last_message_at' => now()->subDays(5),
            ],
        ];

        foreach ($threads as $threadData) {
            Thread::updateOrCreate(
                ['id' => $threadData['id']],
                $threadData
            );
        }
    }
}
