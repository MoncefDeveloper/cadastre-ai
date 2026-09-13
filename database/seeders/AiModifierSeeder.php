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
                'label' => 'Sound More Human',
                'instruction' => 'Eliminate corporate stiffness and robotic AI pleasantries. Rewrite the reply in a warm, conversational, authentic voice as if sent directly from an experienced personal advisor.',
                'color' => 'primary',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'user_id' => null, // Global Modifier
                'label' => 'Convert to Bullet Points',
                'instruction' => 'Structure all property features, pricing, square footage, and key amenities into clean, scannable bullet points for easy reading on mobile devices.',
                'color' => 'info',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'id' => 4,
                'user_id' => null, // Global Modifier
                'label' => 'Add Private Viewing CTA',
                'instruction' => 'Add a clear, high-converting call to action inviting the buyer for an immediate private viewing or exclusive weekend tour before competing offers are presented to the seller.',
                'color' => 'danger',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'id' => 5,
                'user_id' => null, // Global Modifier
                'label' => 'More Executive & Formal',
                'instruction' => 'Elevate the vocabulary using sophisticated real estate advisory terms. Maintain impeccable diplomatic courtesy and confidentiality suitable for ambassadorial and VIP clients.',
                'color' => 'gray',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'id' => 6,
                'user_id' => null, // Global Modifier
                'label' => 'FHA Strict Compliance',
                'instruction' => 'Enforce uncompromising Fair Housing Act (FHA) and Equal Opportunity compliance. Remove all familial status, religious, demographic, or neighborhood-biased descriptions. Use objective property metrics only.',
                'color' => 'success',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'id' => 7,
                'user_id' => null, // Global Modifier
                'label' => 'Emphasize Luxury Amenities',
                'instruction' => 'Highlight architectural craftsmanship, private heated pool/spa, master suite finishes, panoramic views, and prestigious neighborhood status without marketing hyperbole.',
                'color' => 'primary',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'id' => 8,
                'user_id' => null, // Global Modifier
                'label' => 'Warm & Friendly Rapport',
                'instruction' => 'Rewrite the draft with a friendly, welcoming tone. Build personal rapport with the client while maintaining clear professional boundaries.',
                'color' => 'success',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'id' => 9,
                'user_id' => null, // Global Modifier
                'label' => 'Soften Pitch (Less Pushy)',
                'instruction' => 'Remove any aggressive sales pressure. Frame the response as helpful, unpressured guidance, giving the client space to consider their options.',
                'color' => 'warning',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'id' => 10,
                'user_id' => null, // Global Modifier
                'label' => 'Commercial Specs Focus',
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
