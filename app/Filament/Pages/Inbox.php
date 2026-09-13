<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\ManagesAiBrain;
use App\Filament\Pages\Concerns\ManagesChatDraft;
use App\Filament\Pages\Concerns\ManagesInboundSimulator;
use App\Filament\Pages\Concerns\ManagesInboxList;
use App\Models\Message;
use App\Models\Thread;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Livewire\Attributes\Computed;

class Inbox extends Page implements HasForms
{
    use InteractsWithForms,
        ManagesInboxList,
        ManagesChatDraft,
        ManagesAiBrain,
        ManagesInboundSimulator,
        HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';
    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-inbox-stack';

    protected static ?string $navigationLabel = 'AI Shared Inbox';

    protected static ?string $title = 'Inbox';

    protected static ?string $slug = 'inbox';

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = true;

    protected ?string $heading = '';

    protected string $view = 'filament.pages.inbox';

    public ?int $activeThreadId = null;

    public ?Message $activeDraft = null;

    public ?array $draftData = [];

    public ?string $draftUpdatedAt = null;

    public static function getNavigationBadge(): ?string
    {
        $count = Thread::where('is_unread', true)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Unread client conversations requiring triage';
    }

    public function mount(): void
    {
        $requestedThreadId = request()->query('thread');

        if ($requestedThreadId && Thread::where('id', $requestedThreadId)->exists()) {
            $this->loadThread((int) $requestedThreadId);
        } else {
            $latestThread = Thread::latest('last_message_at')->first();
            if ($latestThread) {
                $this->loadThread($latestThread->id);
            }
        }

        $this->initSimulatorForm();
    }

    #[Computed]
    public function activeThread()
    {
        if (! $this->activeThreadId) {
            return null;
        }

        return Thread::with([
            'client',
            // Order newest first so recent replies appear at the top of chat history
            'messages' => fn ($q) => $q->orderBy('created_at', 'desc'),
            'propertyMatches.property.category',
        ])->find($this->activeThreadId);
    }

    public function loadThread(int $threadId): void
    {
        $this->activeThreadId = $threadId;
        $thread = $this->activeThread();

        if ($thread && $thread->is_unread) {
            $thread->update(['is_unread' => false]);
        }

        // Order-agnostic draft detection: prevents index mismatch when messages are sorted desc
        $this->activeDraft = $thread?->messages->firstWhere('is_draft', true);

        $this->isGeneratingDraft = false;
        $this->ratingResult = null;
        $this->isRating = false;

        $this->draftForm->fill([]);
    }

    protected function getForms(): array
    {
        return [
            'draftForm',
            'simulatorForm',
        ];
    }

    public function draftForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                RichEditor::make('body_html')
                    ->hiddenLabel()
                    ->required()
                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList', 'redo', 'undo']),
            ])
            ->statePath('draftData');
    }
}
