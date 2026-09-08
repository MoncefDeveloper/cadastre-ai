<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\Message\MessageDirection;
use App\Models\Message;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

class InboundAiTriageChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected ?string $maxHeight = '280px';

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = '30s';

    public ?string $filter = '30d';

    public function getHeading(): string | Htmlable
    {
        return 'Inbound Inquiries vs. AI Copilot Triage';
    }

    public function getDescription(): ?string
    {
        return 'Real-time velocity of incoming leads against AI draft replies';
    }

    /**
     * 📅 User-Controllable Time Period Filters
     */
    protected function getFilters(): ?array
    {
        return [
            '7d'   => 'Last 7 Days',
            '30d'  => 'Last 30 Days',
            '90d'  => 'Last Quarter',
            'year' => 'This Year',
        ];
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isAgent = $user && $user->hasRole('Senior Agent');

        // =========================================================================
        // 1. RESOLVE DATE RANGE FROM ACTIVE FILTER
        // =========================================================================
        $startDate = match ($this->filter) {
            '7d'   => now()->subDays(6)->startOfDay(),
            '90d'  => now()->subDays(89)->startOfDay(),
            'year' => now()->startOfYear(),
            default => now()->subDays(29)->startOfDay(), // '30d' default
        };
        $endDate = now()->endOfDay();

        // =========================================================================
        // 2. SCOPED BASE QUERIES (Role-Adaptive)
        // =========================================================================
        $baseQuery = Message::query()->whereBetween('created_at', [$startDate, $endDate]);

        if ($isAgent) {
            $baseQuery->whereHas('thread', fn($q) => $q->where('assigned_user_id', $user->id));
        }

        // Query Inbound Client Messages (direction = 1)
        $inboundRaw = (clone $baseQuery)
            ->where('direction', MessageDirection::INBOUND)
            ->selectRaw('DATE(created_at) as dt, count(*) as qty')
            ->groupBy('dt')
            ->pluck('qty', 'dt');

        // Query AI Generated Outbound Replies (is_ai_generated = true)
        $outboundRaw = (clone $baseQuery)
            ->where('direction', MessageDirection::OUTBOUND)
            ->where('is_ai_generated', true)
            ->selectRaw('DATE(created_at) as dt, count(*) as qty')
            ->groupBy('dt')
            ->pluck('qty', 'dt');

        // =========================================================================
        // 3. ZERO-GAP CONTINUOUS DATE MAPPING (X-Axis Normalization)
        // =========================================================================
        $labels = [];
        $inboundData = [];
        $outboundData = [];

        if ($this->filter === 'year') {
            $period = CarbonPeriod::create($startDate, '1 month', $endDate);
            $dateFormat = 'M Y';

            // 🛡️ Cross-Database Driver Compatibility (MySQL DATE_FORMAT vs SQLite strftime)
            $monthFormatSql = DB::connection()->getDriverName() === 'sqlite'
                ? "strftime('%Y-%m', created_at)"
                : "DATE_FORMAT(created_at, '%Y-%m')";

            $inboundMonthly = (clone $baseQuery)
                ->where('direction', MessageDirection::INBOUND)
                ->selectRaw("{$monthFormatSql} as dt, count(*) as qty")
                ->groupBy('dt')
                ->pluck('qty', 'dt');

            $outboundMonthly = (clone $baseQuery)
                ->where('direction', MessageDirection::OUTBOUND)
                ->where('is_ai_generated', true)
                ->selectRaw("{$monthFormatSql} as dt, count(*) as qty")
                ->groupBy('dt')
                ->pluck('qty', 'dt');

            foreach ($period as $date) {
                $labels[] = $date->format($dateFormat);
                $key = $date->format('Y-m');
                $inboundData[] = (int) ($inboundMonthly->get($key, 0));
                $outboundData[] = (int) ($outboundMonthly->get($key, 0));
            }
        } else {
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            $dateFormat = $this->filter === '7d' ? 'D, M j' : 'M j';

            foreach ($period as $date) {
                $labels[] = $date->format($dateFormat);
                $key = $date->format('Y-m-d');
                $inboundData[] = (int) ($inboundRaw->get($key, 0));
                $outboundData[] = (int) ($outboundRaw->get($key, 0));
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Inbound Inquiries',
                    'data' => $inboundData,
                    'borderColor' => '#0284c7', // Sky Blue
                    'backgroundColor' => 'rgba(2, 132, 199, 0.08)',
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderWidth' => 2,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                    'pointBackgroundColor' => '#0284c7',
                ],
                [
                    'label' => 'AI Copilot Replies',
                    'data' => $outboundData,
                    'borderColor' => '#059669', // Emerald Green
                    'backgroundColor' => 'rgba(5, 150, 105, 0.08)',
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderWidth' => 2,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                    'pointBackgroundColor' => '#059669',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
