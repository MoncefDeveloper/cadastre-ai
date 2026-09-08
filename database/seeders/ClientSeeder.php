<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ClientStatus;
use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            // =========================================================================
            // 1. ACTIVE BUYERS & INVESTORS (ClientStatus::ACTIVE)
            // =========================================================================
            [
                'id' => 1,
                'first_name' => 'Sultan',
                'last_name' => 'Al-Otaibi',
                'email' => 'sultan.otaibi@gulf-invest.test',
                'phone' => '+966 50 123 4567',
                'status' => ClientStatus::ACTIVE,
                'source' => 'Inbound Postmark Email', // Looking for Diplomatic Villa in Hydra
            ],
            [
                'id' => 2,
                'first_name' => 'Noura',
                'last_name' => 'Al-Mansoor',
                'email' => 'noura.mansoor@diaspora.test',
                'phone' => '+33 6 12 34 56 78',
                'status' => ClientStatus::ACTIVE,
                'source' => 'WhatsApp Concierge', // Seeking Canastel Sea-View Villa
            ],
            [
                'id' => 3,
                'first_name' => 'Alexander',
                'last_name' => 'Vance',
                'email' => 'a.vance@techventures-eu.test',
                'phone' => '+44 20 7946 0912',
                'status' => ClientStatus::ACTIVE,
                'source' => 'Landing Page Webform', // Seeking Bab Ezzouar Corporate Floor
            ],
            [
                'id' => 4,
                'first_name' => 'Layla',
                'last_name' => 'Al-Khatib',
                'email' => 'layla.khatib@luxury-assets.test',
                'phone' => '+33 1 42 68 55 10',
                'status' => ClientStatus::ACTIVE,
                'source' => 'VIP Referral', // Seeking Avenue Montaigne Penthouse
            ],

            // =========================================================================
            // 2. NEW INCOMING LEADS (ClientStatus::NEW)
            // =========================================================================
            [
                'id' => 5,
                'first_name' => 'Jonathan',
                'last_name' => 'Sterling',
                'email' => 'jsterling@brickellcapital.test',
                'phone' => '+1 (305) 555-8910',
                'status' => ClientStatus::NEW,
                'source' => 'Landing Page Webform', // Seeking Brickell Miami Penthouse
            ],
            [
                'id' => 6,
                'first_name' => 'Camille',
                'last_name' => 'Dubois',
                'email' => 'camille.dubois@luxury-paris.test',
                'phone' => '+33 6 45 89 22 10',
                'status' => ClientStatus::NEW,
                'source' => 'Inbound Postmark Email', // Seeking Cap de Nice Waterfront
            ],
            [
                'id' => 7,
                'first_name' => 'Karim',
                'last_name' => 'Benali',
                'email' => 'karim.benali@diaspora-dz.test',
                'phone' => '+213 550 44 88 12',
                'status' => ClientStatus::NEW,
                'source' => 'WhatsApp Concierge', // Seeking Val d'Hydra Duplex
            ],
            [
                'id' => 8,
                'first_name' => 'Samir',
                'last_name' => 'Hadj-Ali',
                'email' => 'samir.hadjali@commercial-dz.test',
                'phone' => '+213 660 33 22 11',
                'status' => ClientStatus::NEW,
                'source' => 'Landing Page Webform', // Seeking Ain El Turk Land Parcel
            ],

            // =========================================================================
            // 3. CLOSED TRANSACTION CLIENTS (ClientStatus::CLOSED)
            // =========================================================================
            [
                'id' => 9,
                'first_name' => 'Rachel',
                'last_name' => 'Greenbaum',
                'email' => 'rachel.g@sohodesign.test',
                'phone' => '+1 (212) 555-0142',
                'status' => ClientStatus::CLOSED,
                'source' => 'Manual Entry', // Completed SoHo Loft Lease
            ],
            [
                'id' => 10,
                'first_name' => 'Marcus',
                'last_name' => 'Brody',
                'email' => 'marcus.brody@globalholding.test',
                'phone' => '+1 (415) 555-3211',
                'status' => ClientStatus::CLOSED,
                'source' => 'VIP Referral', // Completed Akid Lotfi Apartment Sale
            ],
        ];

        foreach ($clients as $clientData) {
            Client::updateOrCreate(
                ['id' => $clientData['id']],
                $clientData
            );
        }
    }
}
