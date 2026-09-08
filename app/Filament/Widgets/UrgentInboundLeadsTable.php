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
            ->columns([
                // 1. Client Identity & Source
                TextColumn::make('client.first_name')
                    ->label('Client')
                    ->state(fn (Thread $record): string => trim(($record->client?->first_name ?? 'Unknown') . ' ' . ($record->client?->last_name ?? '')))
                    ->description(fn (Thread $record): string => $record->client?->email ?? 'No email')
                    ->icon('heroicon-m-user')
                    ->weight('bold')
                    ->searchable(),

                // 2. Inquiry Subject & Channel Icon
                TextColumn::make('subject')
                    ->label('Inquiry Subject')
                    ->limit(38)
                    ->description(fn (Thread $record): string => match ($record->channel) {
                        ThreadChannel::EMAIL => 'Inbound Postmark Email',
                        ThreadChannel::WHATSAPP => 'WhatsApp Concierge',
                        ThreadChannel::WEBFORM => 'Portal Webform',
                        default => 'Direct Channel',
                    })
                    ->icon(fn (Thread $record): string => match ($record->channel) {
                        ThreadChannel::EMAIL => 'heroicon-m-envelope',
                        ThreadChannel::WHATSAPP => 'heroicon-m-chat-bubble-left-right',
                        ThreadChannel::WEBFORM => 'heroicon-m-globe-alt',
                        default => 'heroicon-m-inbox',
                    })
                    ->iconColor('primary'),

                // 3. Priority Badge
                TextColumn::make('priority')
                    ->badge()
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

                // 4. Top AI Property Match (The AI Showcase)
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
                    ->description(function (Thread $record): ?string {
                        $topMatch = $record->propertyMatches->first();
                        if (! $topMatch || ! $topMatch->property) return null;
                        return str($topMatch->property->title)->limit(32)->toString();
                    }),

                // 5. Relative Timing
                TextColumn::make('last_message_at')
                    ->label('Last Message')
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('openInbox')
                    ->label('Open in Inbox')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('primary')
                    ->outlined()
                    ->size('sm')
                    ->url(fn (Thread $record): string => url('/admin/inbox') . '?thread=' . $record->id),
            ]);
    }
}
