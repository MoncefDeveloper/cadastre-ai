<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Property\ListingType;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // 10 Deterministic Properties Assigned to Agent Demo (User ID 4)
        $properties = [
            // =========================================================================
            // 1. ALGIERS (HYDRA / EL BIAR / BAB EZZOUAR)
            // =========================================================================
            [
                'id' => 1,
                'agent_id' => 4, // Agent Demo
                'category_id' => 2, // Luxury Villas & Penthouses
                'listing_type' => ListingType::SALE,
                'property_type' => PropertyType::VILLA,
                'status' => PropertyStatus::AVAILABLE,
                'title' => 'The Grand Hydra Estate — Diplomatic Compound',
                'slug' => 'the-grand-hydra-estate-diplomatic-compound',
                'description' => "### Exceptional Diplomatic Estate in Prime Hydra\n\nSituated in the prestigious embassy district of **Hydra, Algiers**, this masterfully designed contemporary residence offers unmatched privacy and refined luxury.\n\n* **Features & Highlights:**\n  * 850 sqm of bespoke interior living space on a 1,400 sqm landscaped parcel\n  * Private heated swimming pool, hammam, and wellness spa\n  * 6 ensuite bedrooms with imported Italian marble and walk-in dressing suites\n  * Comprehensive smart home automation, high-security surveillance, and biometric access\n  * Underground garage accommodating 6 vehicles plus dedicated staff quarters\n\n*Ideal for diplomatic delegations, multinational executive leadership, or private family compound.*",
                'city' => 'Algiers',
                'address' => 'Chemin Doudou Mokhtar, Hydra',
                'price' => 4_800_000 * 100, // $4,800,000 stored in cents
                'discount_price' => 4_500_000 * 100, // $4,500,000 in cents
                'area_sqm' => 850,
                'bedrooms' => 6,
                'bathrooms' => 7,
                'is_featured' => true,
            ],
            [
                'id' => 2,
                'agent_id' => 4,
                'category_id' => 1, // Residential Homes & Duplexes
                'listing_type' => ListingType::SALE,
                'property_type' => PropertyType::APARTMENT,
                'status' => PropertyStatus::AVAILABLE,
                'title' => 'Modern Duplex Residence with Private Garden',
                'slug' => 'modern-duplex-residence-private-garden-el-biar',
                'description' => "### Sophisticated Duplex in Val d'Hydra / El Biar\n\nA rare luxury duplex combining the comfort of an individual villa with the security of a boutique gated residence.\n\n* **Property Features:**\n  * 240 sqm duplex with open-concept double living room\n  * 80 sqm private terrace and Mediterranean landscaped garden\n  * Master suite with jacuzzi, dressing room, and balcony\n  * Fully fitted German kitchen with integrated Miele appliances\n  * 2 underground parking spots and private storage cellar.",
                'city' => 'Algiers',
                'address' => 'Val d\'Hydra, El Biar',
                'price' => 1_250_000 * 100, // $1,250,000 in cents
                'discount_price' => 1_190_000 * 100,
                'area_sqm' => 240,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'is_featured' => false,
            ],
            [
                'id' => 3,
                'agent_id' => 4,
                'category_id' => 3, // Commercial & Prime Offices
                'listing_type' => ListingType::RENT,
                'property_type' => PropertyType::COMMERCIAL,
                'status' => PropertyStatus::AVAILABLE,
                'title' => 'Grade-A Corporate Headquarters Floor in Bab Ezzouar',
                'slug' => 'grade-a-corporate-headquarters-bab-ezzouar',
                'description' => "### Premium Corporate Floor in Algiers Financial Hub\n\nLocated in the prime business park of **Bab Ezzouar**, directly adjacent to the international airport corridor.\n\n* **Corporate Specifications:**\n  * 750 sqm modular open-plan floor plan with high-speed fiber backbone\n  * 4 executive boardrooms with video-conference acoustics\n  * Dedicated server room with independent climate control\n  * 15 reserved underground parking bays\n  * 24/7 security with dual-redundant power generators.",
                'city' => 'Algiers',
                'address' => 'Quartier d\'Affaires, Bab Ezzouar',
                'price' => 12_000 * 100, // $12,000/mo in cents
                'discount_price' => null,
                'area_sqm' => 750,
                'bedrooms' => 0,
                'bathrooms' => 6,
                'is_featured' => false,
            ],

            // =========================================================================
            // 2. ORAN (CANASTEL / AKID LOTFI / AIN EL TURK)
            // =========================================================================
            [
                'id' => 4,
                'agent_id' => 4,
                'category_id' => 4, // Mediterranean & Vacation Rentals
                'listing_type' => ListingType::RENT,
                'property_type' => PropertyType::VILLA,
                'status' => PropertyStatus::AVAILABLE,
                'title' => 'Panoramic Coastal Villa with Infinity Pool in Canastel',
                'slug' => 'panoramic-coastal-villa-infinity-pool-canastel',
                'description' => "### Cliffside Mediterranean Sanctuary\n\nOverlooking the breathtaking Oran coastline, this designer villa offers unobstructed sea panoramas from every room.\n\n* **Highlights:**\n  * Infinity pool merging seamlessly with the Mediterranean horizon\n  * 420 sqm of sun-drenched architectural living spaces\n  * Expansive teak solarium terrace and outdoor barbecue pergola\n  * Master suite with panoramic floor-to-ceiling glass\n  * Direct private path access to the coastline.",
                'city' => 'Oran',
                'address' => 'Boulevard des Falaises, Canastel',
                'price' => 4_500 * 100, // $4,500/mo in cents
                'discount_price' => null,
                'area_sqm' => 420,
                'bedrooms' => 4,
                'bathrooms' => 4,
                'is_featured' => true,
            ],
            [
                'id' => 5,
                'agent_id' => 4,
                'category_id' => 1, // Residential Homes & Duplexes
                'listing_type' => ListingType::SALE,
                'property_type' => PropertyType::APARTMENT,
                'status' => PropertyStatus::SOLD,
                'title' => 'High-Rise Luxury Apartment in Akid Lotfi',
                'slug' => 'high-rise-luxury-apartment-akid-lotfi',
                'description' => "### Modern Urban Living in Vibrant Oran\n\nPrime 165 sqm apartment located in a luxury secured residence with elevator, caretaker, and underground parking.\n\n* 3 spacious bedrooms including an ensuite master\n* Fully renovated with Spanish porcelain flooring\n* Panoramic double balcony overlooking the city skyline.",
                'city' => 'Oran',
                'address' => 'Boulevard Principal, Akid Lotfi',
                'price' => 380_000 * 100, // $380,000 in cents
                'discount_price' => null,
                'area_sqm' => 165,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'is_featured' => false,
            ],
            [
                'id' => 6,
                'agent_id' => 4,
                'category_id' => 5, // Land & Development Plots
                'listing_type' => ListingType::SALE,
                'property_type' => PropertyType::LAND,
                'status' => PropertyStatus::AVAILABLE,
                'title' => 'Prime Waterfront Development Parcel in Ain El Turk',
                'slug' => 'prime-waterfront-development-parcel-ain-el-turk',
                'description' => "### Premier Coastal Development Opportunity\n\nA 2,500 sqm zoned beachfront parcel ideal for a boutique luxury resort, private residence compound, or high-end residential apartments.\n\n* Direct sea frontage with legal architectural permits\n* All utilities (water, 3-phase electricity, high-speed telecom) at parcel boundary\n* Clear freehold ownership title ready for immediate notary transfer.",
                'city' => 'Oran',
                'address' => 'Route de la Corniche, Ain El Turk',
                'price' => 1_800_000 * 100, // $1,800,000 in cents
                'discount_price' => null,
                'area_sqm' => 2500,
                'bedrooms' => 0,
                'bathrooms' => 0,
                'is_featured' => false,
            ],

            // =========================================================================
            // 3. FRANCE (PARIS & FRENCH RIVIERA)
            // =========================================================================
            [
                'id' => 7,
                'agent_id' => 4,
                'category_id' => 2, // Luxury Villas & Penthouses
                'listing_type' => ListingType::SALE,
                'property_type' => PropertyType::APARTMENT,
                'status' => PropertyStatus::AVAILABLE,
                'title' => 'Haussmannian Penthouse on Avenue Montaigne',
                'slug' => 'haussmannian-penthouse-avenue-montaigne-paris',
                'description' => "### Parisian Elegance in the Golden Triangle\n\nCommanding top-floor views of the Eiffel Tower, this quintessential Parisian penthouse features 3.5m ceilings, authentic chevron oak parquet, and intricate marble fireplaces.\n\n* 280 sqm corner penthouse with continuous wrap-around balcony\n* 4 bedroom suites with private dressing rooms\n* Bespoke French kitchen with wine cellar and service quarters\n* Full concierge service and elevator keyed directly to private landing.",
                'city' => 'Paris',
                'address' => 'Avenue Montaigne, 8th Arrondissement',
                'price' => 3_600_000 * 100, // €3,600,000 in cents
                'discount_price' => null,
                'area_sqm' => 280,
                'bedrooms' => 4,
                'bathrooms' => 4,
                'is_featured' => true,
            ],
            [
                'id' => 8,
                'agent_id' => 4,
                'category_id' => 4, // Mediterranean & Vacation Rentals
                'listing_type' => ListingType::SALE,
                'property_type' => PropertyType::VILLA,
                'status' => PropertyStatus::UNDER_OFFER,
                'title' => 'Cap de Nice Waterfront Villa with Private Boat Slip',
                'slug' => 'cap-de-nice-waterfront-villa-private-boat-slip',
                'description' => "### Iconic Belle Époque Waterfront Villa\n\nPerched directly on the cliffs of Cap de Nice with private mooring access.\n\n* 510 sqm interior living space across 3 elevator-connected levels\n* Heated seawater infinity pool overlooking Villefranche Bay\n* Independent guest villa and caretaker apartment\n* Private funicular to the waterfront.",
                'city' => 'Nice',
                'address' => 'Boulevard Princesse Grâce de Monaco, Cap de Nice',
                'price' => 5_900_000 * 100, // €5,900,000 in cents
                'discount_price' => null,
                'area_sqm' => 510,
                'bedrooms' => 5,
                'bathrooms' => 5,
                'is_featured' => true,
            ],

            // =========================================================================
            // 4. US FLAGSHIP SHOWCASES (MIAMI & NEW YORK)
            // =========================================================================
            [
                'id' => 9,
                'agent_id' => 4,
                'category_id' => 2, // Luxury Villas & Penthouses
                'listing_type' => ListingType::SALE,
                'property_type' => PropertyType::APARTMENT,
                'status' => PropertyStatus::AVAILABLE,
                'title' => 'Brickell Avenue Sky Penthouse with Biscayne Bay Views',
                'slug' => 'brickell-avenue-sky-penthouse-miami',
                'description' => "### Ultra-Modern Sky Mansion in Miami Financial District\n\nOccupying the 54th floor with 12-foot floor-to-ceiling glass and panoramic views of Biscayne Bay and the Miami skyline.\n\n* 320 sqm living space with 100 sqm private rooftop plunge pool terrace\n* Italian Poliform kitchen and Sub-Zero / Wolf appliance suite\n* 3 parking spaces with valet and private marina access.",
                'city' => 'Miami',
                'address' => '1421 Brickell Avenue, Miami',
                'price' => 2_850_000 * 100, // $2,850,000 in cents
                'discount_price' => null,
                'area_sqm' => 320,
                'bedrooms' => 3,
                'bathrooms' => 4,
                'is_featured' => true,
            ],
            [
                'id' => 10,
                'agent_id' => 4,
                'category_id' => 2, // Luxury Villas & Penthouses
                'listing_type' => ListingType::RENT,
                'property_type' => PropertyType::APARTMENT,
                'status' => PropertyStatus::RENTED,
                'title' => 'SoHo Historic Cast-Iron Loft on Spring Street',
                'slug' => 'soho-historic-cast-iron-loft-spring-street-nyc',
                'description' => "### Classic Architectural Cast-Iron Loft in Prime SoHo\n\nFeatures 14-foot original timber beam ceilings, exposed brick masonry, and oversized Corinthian-columned windows.\n\n* 290 sqm full-floor loft with keyed direct elevator entry\n* Primary suite with custom Italian dressing room and marble wet room\n* Located in the heart of SoHo's premier gallery and fashion enclave.",
                'city' => 'New York',
                'address' => 'Spring & Mercer Street, SoHo',
                'price' => 16_500 * 100, // $16,500/mo in cents
                'discount_price' => null,
                'area_sqm' => 290,
                'bedrooms' => 2,
                'bathrooms' => 3,
                'is_featured' => false,
            ],
        ];

        // Seed 10 Properties
        foreach ($properties as $propertyData) {
            Property::updateOrCreate(
                ['id' => $propertyData['id']],
                $propertyData
            );
        }

        // =========================================================================
        // Seed 20 Property Images (2 images per property, IDs 1 - 20)
        // =========================================================================
        $sampleImages = [
            'properties/home_placeholder.png',
            'properties/home_placeholder_2.png',
            'properties/home_placeholder_3.png',
            'properties/home_placeholder_4.png',
        ];

        $imageId = 1;
        for ($propertyId = 1; $propertyId <= 10; $propertyId++) {
            for ($sort = 1; $sort <= 2; $sort++) {
                $imagePath = $sampleImages[($imageId - 1) % count($sampleImages)];

                PropertyImage::updateOrCreate(
                    ['id' => $imageId],
                    [
                        'property_id' => $propertyId,
                        'image_path' => $imagePath,
                        'sort_order' => $sort,
                    ]
                );

                $imageId++;
            }
        }
    }
}
