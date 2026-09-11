<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\Thread\ThreadChannel;
use App\Enums\Thread\ThreadPriority;
use App\Enums\Thread\ThreadStatus;
use App\Models\Thread;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class UrgentInboundLeadsTable extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = '30s';

    public function getTableHeading(): string | Htmlable | null
    {
        return 'High-Priority Inquiries & AI Recommendation Ledger';
    }

    public function getTableDescription(): string | Htmlable | null
    {
        return 'Real-time inbound lead triage prioritized by urgency and AI property match confidence scores';
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isAgent = $user && $user->hasRole('Senior Agent');

        return $table
            ->query(function () use ($user, $isAgent): Builder {
                $query = Thread::query()
                    ->with([
                        'client',
                        'propertyMatches' => fn ($q) => $q->where('is_rejected', false)->orderByDesc('match_score'),
                        'propertyMatches.property',
                    ])
                    ->where('status', '!=', ThreadStatus::CLOSED)
                    ->orderByRaw('CASE WHEN priority = 3 THEN 1 WHEN priority = 2 THEN 2 ELSE 3 END')
                    ->orderByDesc('is_unread')
                    ->orderByDesc('last_message_at');

                if ($isAgent) {
                    $query->where('assigned_user_id', $user->id);
                }

                return $query;
            })
            ->paginated(false)
            ->emptyStateHeading('Inbox Zero: No Urgent Inquiries')
            ->emptyStateDescription('All high-priority inbound conversation threads have been successfully triaged and assigned.')
            ->emptyStateIcon('heroicon-o-check-badge')
            ->columns([
                // 1. Client Identity & Source
                TextColumn::make('client.first_name')
                    ->label('Client')
                    ->state(fn (Thread $record): string => trim(($record->client?->first_name ?? 'Unknown') . ' ' . ($record->client?->last_name ?? '')))
                    ->description(fn (Thread $record): string => $record->client?->email ?? 'No email')
                    ->icon('heroicon-m-user')
                    ->weight('bold')
                    ->searchable()
                    ->tooltip(fn (Thread $record): string => trim(($record->client?->first_name ?? '') . ' ' . ($record->client?->last_name ?? '')) . ' (' . ($record->client?->email ?? 'No email') . ')'),

                // 2. Inquiry Subject (No Icon, Standardized 30-Char Limit + Full Tooltip)
                TextColumn::make('subject')
                    ->label('Inquiry Subject')
                    ->limit(30)
                    ->tooltip(fn (Thread $record): ?string => $record->subject)
                    ->description(fn (Thread $record): string => match ($record->channel) {
                        ThreadChannel::EMAIL => 'Inbound Postmark Email',
                        ThreadChannel::WHATSAPP => 'WhatsApp Concierge',
                        ThreadChannel::WEBFORM => 'Portal Webform',
                        default => 'Direct Channel',
                    }),

                // 3. Priority Badge (Standardized: Centered)
                TextColumn::make('priority')
                    ->badge()
                    ->alignCenter()
                    ->formatStateUsing(fn (ThreadPriority $state): string => match ($state) {
                        ThreadPriority::URGENT => 'Urgent',
                        ThreadPriority::HIGH => 'High Priority',
                        ThreadPriority::NORMAL => 'Normal',
                    })
                    ->color(fn (ThreadPriority $state): string => match ($state) {
                        ThreadPriority::URGENT => 'danger',
                        ThreadPriority::HIGH => 'warning',
                        ThreadPriority::NORMAL => 'gray',
                    }),

                // 4. Top AI Property Match (Kept Left for Multi-line Layout + Dual Description Tooltip)
                TextColumn::make('top_match')
                    ->label('Top AI Property Match')
                    ->state(function (Thread $record): string {
                        $topMatch = $record->propertyMatches->first();

                        if (! $topMatch || ! $topMatch->property) {
                            return 'Pending AI Matching';
                        }

                        return "{$topMatch->match_score}% Match";
                    })
                    ->badge()
                    ->color(function (Thread $record): string {
                        $topMatch = $record->propertyMatches->first();
                        if (! $topMatch) return 'gray';
                        if ($topMatch->match_score >= 90) return 'success';
                        if ($topMatch->match_score >= 75) return 'warning';
                        return 'gray';
                    })
                    ->description(function (Thread $record): ?HtmlString {
                        $topMatch = $record->propertyMatches->first();
                        if (! $topMatch || ! $topMatch->property) {
                            return null;
                        }

                        $fullTitle = (string) $topMatch->property->title;
                        $truncatedTitle = str($fullTitle)->limit(30)->toString();

                        // Native tooltip wrapper on the stacked descriptive subtitle
                        return new HtmlString('<span title="' . e($fullTitle) . '" class="cursor-help underline decoration-dotted decoration-gray-400 dark:decoration-gray-600">' . e($truncatedTitle) . '</span>');
                    })
                    ->tooltip(function (Thread $record): ?string {
                        $topMatch = $record->propertyMatches->first();
                        if (! $topMatch || ! $topMatch->property) {
                            return null;
                        }

                        $reasoning = $topMatch->reasoning ? "\nReasoning: {$topMatch->reasoning}" : '';
                        return "Property: {$topMatch->property->title}{$reasoning}";
                    }),

                // 5. Relative Timing (Standardized: Centered + Exact Date Tooltip)
                TextColumn::make('last_message_at')
                    ->label('Last Message')
                    ->alignCenter()
                    ->since()
                    ->tooltip(fn (Thread $record): ?string => $record->last_message_at?->format('M d, Y - h:i A'))
                    ->sortable(),
            ])
            ->recordActions([
                // Standardized Action Button: outlined, sm size, sm iconSize
                Action::make('openInbox')
                    ->label('Open in Inbox')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('primary')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm')
                    ->url(fn (Thread $record): string => url('/admin/inbox') . '?thread=' . $record->id),
            ]);
    }
}
