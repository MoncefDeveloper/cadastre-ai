<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ThreadPropertyMatch;
use Illuminate\Database\Seeder;

class ThreadPropertyMatchSeeder extends Seeder
{
    public function run(): void
    {
        $matches = [
            // =========================================================================
            // THREAD 1 MATCHES: Sultan Al-Otaibi (Hydra Diplomatic Search)
            // =========================================================================
            [
                'id' => 1,
                'thread_id' => 1, // Sultan Al-Otaibi Thread
                'property_id' => 1, // The Grand Hydra Estate ($4.8M)
                'match_score' => 98.50,
                'reasoning' => 'Perfect diplomatic match: Located in the prime Hydra embassy sector, featuring 6 ensuite bedrooms, heated swimming pool, 6-car underground parking, and standalone security quarters within client\'s $5.0M budget.',
                'is_rejected' => false,
            ],
            [
                'id' => 2,
                'thread_id' => 1,
                'property_id' => 2, // Val d'Hydra Duplex ($1.25M)
                'match_score' => 84.00,
                'reasoning' => 'Viable alternative in adjacent El Biar/Hydra corridor with 240 sqm living space and private Mediterranean garden, though offering 3 bedrooms instead of the requested 6.',
                'is_rejected' => false,
            ],
            [
                'id' => 3,
                'thread_id' => 1,
                'property_id' => 6, // Ain El Turk Land ($1.8M)
                'match_score' => 55.00,
                'reasoning' => 'Archived by agent: Coastal development parcel in Oran does not satisfy Algiers diplomatic residential mandate.',
                'is_rejected' => true, // Rejected match test case
            ],

            // =========================================================================
            // THREAD 2 MATCHES: Noura Al-Mansoor (Oran Seaside Rental)
            // =========================================================================
            [
                'id' => 4,
                'thread_id' => 2, // Noura Al-Mansoor Thread
                'property_id' => 4, // Canastel Panoramic Villa ($4,500/mo)
                'match_score' => 97.00,
                'reasoning' => 'Exact criteria match: Cliffside Mediterranean panoramic sea view in Canastel, Oran. 4 bedrooms, infinity pool, high-speed fiber, and 12-month lease at $4,500/mo under client\'s $5,000/mo budget.',
                'is_rejected' => false,
            ],

            // =========================================================================
            // THREAD 3 MATCHES: Alexander Vance (Bab Ezzouar Corporate Office)
            // =========================================================================
            [
                'id' => 5,
                'thread_id' => 3, // Alexander Vance Thread
                'property_id' => 3, // Bab Ezzouar Corporate Floor ($12,000/mo)
                'match_score' => 96.00,
                'reasoning' => 'Prime commercial fit: 750 sqm Grade-A open-plan corporate floor in Bab Ezzouar business park, adjacent to airport corridor, with 15 parking spots and 90-day fit-out period within $15,000/mo budget.',
                'is_rejected' => false,
            ],

            // =========================================================================
            // THREAD 4 MATCHES: Layla Al-Khatib (Paris Avenue Montaigne Penthouse)
            // =========================================================================
            [
                'id' => 6,
                'thread_id' => 4, // Layla Al-Khatib Thread
                'property_id' => 7, // Avenue Montaigne Penthouse (€3.6M)
                'match_score' => 98.00,
                'reasoning' => 'Direct luxury match: Top-floor 280 sqm Haussmannian corner penthouse in the Golden Triangle with Eiffel Tower views, authentic chevron parquet, and private elevator landing under €4.0M budget.',
                'is_rejected' => false,
            ],

            // =========================================================================
            // THREAD 5 MATCHES: Jonathan Sterling (Miami Brickell Sky Penthouse)
            // =========================================================================
            [
                'id' => 7,
                'thread_id' => 5, // Jonathan Sterling Thread
                'property_id' => 9, // Brickell Avenue Sky Penthouse ($2.85M)
                'match_score' => 95.50,
                'reasoning' => 'Turnkey sky mansion: 54th-floor Brickell residence with 320 sqm living area, Biscayne Bay glass panoramas, private plunge pool terrace, and expedited cash escrow closing.',
                'is_rejected' => false,
            ],

            // =========================================================================
            // THREAD 6 MATCHES: Camille Dubois (Nice Cap de Nice Waterfront)
            // =========================================================================
            [
                'id' => 8,
                'thread_id' => 6, // Camille Dubois Thread
                'property_id' => 8, // Cap de Nice Waterfront Villa (€5.9M)
                'match_score' => 96.50,
                'reasoning' => 'Historic Belle Époque waterfront villa on the cliffs of Cap de Nice with private boat slip, 510 sqm interior across 3 elevator-connected levels, and heated seawater infinity pool.',
                'is_rejected' => false,
            ],
        ];

        foreach ($matches as $matchData) {
            ThreadPropertyMatch::updateOrCreate(
                ['id' => $matchData['id']],
                $matchData
            );
        }
    }
}
