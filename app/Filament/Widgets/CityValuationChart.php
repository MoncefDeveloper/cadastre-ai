<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\Property\ListingType;
use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs;

class CityValuationChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected ?string $maxHeight = '280px';

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = '30s';

    public ?string $filter = 'all';

    public function getHeading(): string | Htmlable
    {
        return 'Portfolio Capital Allocation by City';
    }

    public function getDescription(): ?string
    {
        return 'Total active property valuation across primary Algerian, French, and US markets ($ in Millions)';
    }

    protected function getFilters(): ?array
    {
        return [
            'all'  => 'All Listings',
            'sale' => 'For Sale Only',
            'rent' => 'For Rent Only',
        ];
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isAgent = $user && $user->hasRole('Senior Agent');
        $isGuest = $user && ($user->id === 5 || $user->hasRole('Guest Viewer'));

        // 1. Role-Adaptive Query
        $query = Property::where('status', PropertyStatus::AVAILABLE);

        if ($isAgent) {
            $query->where('agent_id', $user->id);
        }

        // 2. Apply Listing Filter
        match ($this->filter) {
            'sale'  => $query->where('listing_type', ListingType::SALE),
            'rent'  => $query->where('listing_type', ListingType::RENT),
            default => null,
        };

        // 3. Aggregate sum of price (in cents) grouped by City
        $cityAggregates = $query
            ->select('city', DB::raw('SUM(price) as total_cents'), DB::raw('COUNT(*) as property_count'))
            ->groupBy('city')
            ->orderByDesc('total_cents')
            ->get();

        $labels = [];
        $valuesInMillions = [];

        foreach ($cityAggregates as $item) {
            $city = $item->city;
            $count = (int) $item->property_count;
            $dollars = (float) ($item->total_cents / 100);
            $unitLabel = str('unit')->plural($count); // "unit" for 1, "units" for 2+

            if ($isGuest) {
                $labels[] = "{$city} ({$count} {$unitLabel})";
                $valuesInMillions[] = $count;
            } else {
                $millions = round($dollars / 1_000_000, 2);
                $labels[] = "{$city} ({$count} {$unitLabel})";
                $valuesInMillions[] = $millions;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => $isGuest ? 'Active Units' : 'Total Capital ($ Millions)',
                    'data' => $valuesInMillions,
                    'backgroundColor' => [
                        'rgba(245, 158, 11, 0.85)', // Amber (Algiers)
                        'rgba(2, 132, 199, 0.85)',  // Sky Blue (Paris)
                        'rgba(16, 185, 129, 0.85)', // Emerald (Nice)
                        'rgba(124, 58, 237, 0.85)', // Purple (Oran)
                        'rgba(244, 63, 94, 0.85)',  // Rose (Miami)
                        'rgba(100, 116, 139, 0.85)', // Slate (New York)
                    ],
                    'borderColor' => 'transparent',
                    'borderRadius' => 6, // Smooth modern rounded bar corners
                    'borderSkipped' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): RawJs
    {
        $isGuest = auth()->user() && (auth()->id() === 5 || auth()->user()->hasRole('Guest Viewer'));

        $tooltipCallback = $isGuest
            ? "(context) => context.raw + ' Active Units'"
            : "(context) => '$' + context.raw + ' Million'";

        $tickCallback = $isGuest
            ? "(value) => value + ' units'"
            : "(value) => '$' + value + 'M'";

        return RawJs::make(<<<JS
    {
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: {$tooltipCallback}
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: {$tickCallback}
                }
            }
        }
    }
    JS);
    }
}
