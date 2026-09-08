<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\Thread\ThreadChannel;
use App\Models\Thread;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

class OmnichannelEngagementChart extends ChartWidget
{
    protected static ?int $sort = 4;

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
        return 'Omnichannel Inbound Routing';
    }

    public function getDescription(): ?string
    {
        return 'Live distribution of client inquiries by communication channel';
    }

    protected function getFilters(): ?array
    {
        return [
            '7d'  => 'Last 7 Days',
            '30d' => 'Last 30 Days',
            'all' => 'All Time',
        ];
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isAgent = $user && $user->hasRole('Senior Agent');

        // 1. Role-Adaptive Query
        $query = Thread::query();

        if ($isAgent) {
            $query->where('assigned_user_id', $user->id);
        }

        // 2. Apply Time Filter
        $startDate = match ($this->filter) {
            '7d'  => now()->subDays(6)->startOfDay(),
            '30d' => now()->subDays(29)->startOfDay(),
            default => null,
        };

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        // 3. Aggregate Channel Counts
        $counts = $query
            ->select('channel', DB::raw('COUNT(*) as qty'))
            ->groupBy('channel')
            ->pluck('qty', 'channel');

        $emailCount = (int) $counts->get(ThreadChannel::EMAIL->value, 0);
        $waCount    = (int) $counts->get(ThreadChannel::WHATSAPP->value, 0);
        $webCount   = (int) $counts->get(ThreadChannel::WEBFORM->value, 0);

        return [
            'datasets' => [
                [
                    'label' => 'Active Inquiries',
                    'data' => [
                        $emailCount,
                        $waCount,
                        $webCount,
                    ],
                    'backgroundColor' => [
                        'rgba(2, 132, 199, 0.65)',   // Sky Blue (Email)
                        'rgba(16, 185, 129, 0.65)',  // Emerald Green (WhatsApp)
                        'rgba(124, 58, 237, 0.65)',  // Violet (Portal Webform)
                    ],
                    'borderColor' => [
                        '#0284c7',
                        '#10b981',
                        '#7c3aed',
                    ],
                    'borderWidth' => 2,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => [
                "Postmark Email ({$emailCount})",
                "WhatsApp Concierge ({$waCount})",
                "Portal Webform ({$webCount})",
            ],
        ];
    }

    protected function getType(): string
    {
        // 🚀 The Wow Factor: Polar Area Chart
        return 'polarArea';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            plugins: {
                legend: {
                    display: true,
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: (context) => ' ' + context.raw + ' Active Threads'
                    }
                }
            },
            scales: {
                r: {
                    ticks: {
                        display: false, // Hides the ugly numeric scale in the center
                        backdropColor: 'transparent'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)', // Subtle dark-mode spider web
                        circular: true
                    },
                    angleLines: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    }
                }
            },
            layout: {
                padding: { top: 10, bottom: 10 }
            }
        }
        JS);
    }
}
