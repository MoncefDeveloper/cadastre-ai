<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\Property\ListingType;
use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class PropertyInventoryChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected ?string $maxHeight = '280px';

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = '30s';

    public ?string $filter = 'all';

    protected ?string $emptyStateHeading = 'No listings match this filter';

    protected ?string $emptyStateDescription = 'Try selecting another listing category or create a new property.';

    public function getHeading(): string | Htmlable
    {
        return 'Property Inventory Distribution';
    }

    public function getDescription(): ?string
    {
        return 'Live portfolio spread across deal stages and acquisition types';
    }

    /**
     * 🏷️ User-Controllable Listing Type Filters
     */
    protected function getFilters(): ?array
    {
        return [
            'all'  => 'All Listings (Sale & Rent)',
            'sale' => 'For Sale Only',
            'rent' => 'For Rent Only',
        ];
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isAgent = $user && $user->hasRole('Senior Agent');

        // =========================================================================
        // 1. SCOPED BASE QUERY (Role-Adaptive)
        // =========================================================================
        $query = Property::query();

        if ($isAgent) {
            $query->where('agent_id', $user->id);
        }

        // =========================================================================
        // 2. APPLY LISTING-TYPE FILTER
        // =========================================================================
        match ($this->filter) {
            'sale'  => $query->where('listing_type', ListingType::SALE),
            'rent'  => $query->where('listing_type', ListingType::RENT),
            default => null,
        };

        // =========================================================================
        // 3. STATUS AGGREGATION GROUP BY
        // =========================================================================
        $counts = $query->selectRaw('status, count(*) as qty')
            ->groupBy('status')
            ->pluck('qty', 'status');

        // =========================================================================
        // 4. MAP INTEGER-BACKED ENUM VALUES (Zero Missing Categories)
        // =========================================================================
        $availableCount  = (int) ($counts->get(PropertyStatus::AVAILABLE->value, 0));
        $underOfferCount = (int) ($counts->get(PropertyStatus::UNDER_OFFER->value, 0));
        $soldCount       = (int) ($counts->get(PropertyStatus::SOLD->value, 0));
        $rentedCount     = (int) ($counts->get(PropertyStatus::RENTED->value, 0));

        return [
            'datasets' => [
                [
                    'label' => 'Properties',
                    'data' => [
                        $availableCount,
                        $underOfferCount,
                        $soldCount,
                        $rentedCount,
                    ],
                    'backgroundColor' => [
                        '#059669', // Emerald Green (Available)
                        '#d97706', // Amber Gold (Under Offer)
                        '#e11d48', // Rose Red (Sold)
                        '#64748b', // Slate Gray (Rented)
                    ],
                    'borderColor' => '#141416', // Clean gap matching dark card background
                    'borderWidth' => 3,
                    'hoverOffset' => 5,
                ],
            ],
            'labels' => [
                "Available ({$availableCount})",
                "Under Offer ({$underOfferCount})",
                "Sold ({$soldCount})",
                "Rented ({$rentedCount})",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function isEmpty(): bool
    {
        $data = $this->getCachedData();

        return empty(array_filter($data['datasets'][0]['data'] ?? []));
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '72%', // Modern sleek thin-ring doughnut
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'padding' => 14,
                    ],
                ],
            ],
        ];
    }
}
