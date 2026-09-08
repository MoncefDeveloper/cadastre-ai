<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiModifier;
use Illuminate\Database\Seeder;

class AiModifierSeeder extends Seeder
{
    public function run(): void
    {
        $modifiers = [
            [
                'id' => 1,
                'user_id' => null, // Global Modifier
                'label' => 'Make it Shorter',
                'instruction' => 'Condense this draft strictly under 45 words. Strip out all filler text, pleasantries, and background, keeping only the direct answer, property pricing/address, and call-to-action.',
                'color' => 'warning',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'user_id' => null, // Global Modifier
                'label' => 'FHA Strict Compliance',
                'instruction' => 'Enforce uncompromising Fair Housing Act (FHA) and Equal Opportunity compliance. Remove all familial status, religious, demographic, or neighborhood-biased descriptions. Use objective property metrics only.',
                'color' => 'success',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'user_id' => null, // Global Modifier
                'label' => 'Persuasive Closing Pitch',
                'instruction' => 'Inject strong persuasive sales psychology. Highlight the scarcity of this specific listing, recent competitive market demand in this district, and the clear financial advantage of moving quickly.',
                'color' => 'primary',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'id' => 4,
                'user_id' => null, // Global Modifier
                'label' => 'Emphasize Luxury Amenities',
                'instruction' => 'Elevate the vocabulary using luxury real estate editorial phrasing. Highlight architectural craftsmanship, private heated pool/spa, master suite finishes, panoramic views, and prestigious neighborhood status.',
                'color' => 'primary',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'id' => 5,
                'user_id' => null, // Global Modifier
                'label' => 'Urgent Viewing Call to Action',
                'instruction' => 'Add an urgent, high-converting call to action inviting the buyer for an immediate private tour or exclusive weekend viewing before competing offers are presented to the seller.',
                'color' => 'danger',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'id' => 6,
                'user_id' => null, // Global Modifier
                'label' => 'Investment ROI & Yield Focus',
                'instruction' => 'Highlight the analytical financial upside: projected gross rental yield, capital appreciation trajectory in this submarket, and flexible notary payment milestones.',
                'color' => 'success',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'id' => 7,
                'user_id' => null, // Global Modifier
                'label' => 'Diplomatic Counter-Offer Tone',
                'instruction' => 'Structure the reply with a firm commercial negotiation posture while maintaining complete diplomatic courtesy. Reiterate the intrinsic valuation of the asset without appearing aggressive.',
                'color' => 'gray',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'id' => 8,
                'user_id' => null, // Global Modifier
                'label' => 'Warm & Approachable Tone',
                'instruction' => 'Rewrite the draft with a friendly, welcoming, and hospitable tone. Build personal rapport with the client while maintaining clear professional boundaries.',
                'color' => 'primary',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'id' => 9,
                'user_id' => null, // Global Modifier
                'label' => 'French Real Estate Terminology',
                'instruction' => 'Ensure all French property terms (acte notarié, charges de copropriété, DPE, livret de famille, surface carrez) are accurate and formatted for Francophone and Diaspora buyers.',
                'color' => 'warning',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'id' => 10,
                'user_id' => null, // Global Modifier
                'label' => 'Commercial & Technical Specs',
                'instruction' => 'Focus strictly on commercial real estate specifications: floor-loading capacities, fiber optic backbone, HVAC zoning, elevator access ratios, and designated parking allocations.',
                'color' => 'gray',
                'sort_order' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($modifiers as $modifierData) {
            AiModifier::updateOrCreate(
                ['id' => $modifierData['id']],
                $modifierData
            );
        }
    }
}
