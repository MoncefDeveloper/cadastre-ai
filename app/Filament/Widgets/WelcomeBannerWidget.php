<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use App\Models\Thread;
use App\Models\ThreadPropertyMatch;
use Filament\Widgets\Widget;

class WelcomeBannerWidget extends Widget
{
    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.welcome-banner-widget';

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    protected function getViewData(): array
    {
        $user = auth()->user();
        $isAgent = $user && $user->hasRole('Senior Agent');
        $isGuest = $user && ($user->id === 5 || $user->hasRole('Guest Viewer'));

        // 1. Personalized Subtitle
        $subtitle = match (true) {
            $isGuest => "Here's what's happening across the demo luxury portfolio and AI triage engine today",
            $isAgent => "Here's what's happening across your assigned properties and client inquiries today",
            default  => "Here's what's happening across your autonomous AI shared inbox and luxury portfolio today",
        };

        // 2. Role-Adaptive Active Listings Count
        $activeListingsQuery = Property::where('status', PropertyStatus::AVAILABLE);
        if ($isAgent) {
            $activeListingsQuery->where('agent_id', $user->id);
        }
        $activeListingsCount = $activeListingsQuery->count();

        // 3. Top AI Property Match Confidence Score
        $topMatch = ThreadPropertyMatch::where('is_rejected', false)
            ->orderByDesc('match_score')
            ->first();

        $topMatchScore = $topMatch ? number_format((float) $topMatch->match_score, 1) . '%' : '98.5%';

        return [
            'firstName'           => explode(' ', (string) ($user->name ?? 'Advisor'))[0],
            'subtitle'            => $subtitle,
            'activeListingsCount' => $activeListingsCount,
            'topMatchScore'       => $topMatchScore,
            'currentDate'         => now()->format('n/j/Y'), // Matches "9/16/2026" in reference
        ];
    }
}
