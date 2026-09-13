<?php

declare(strict_types=1);

namespace App\Filament\Pages\Concerns;

use App\Enums\Message\MessageDirection;
use App\Jobs\DraftAiResponseJob;
use App\Jobs\SendOutboundEmailJob;
use App\Models\Message;
use App\Models\Thread;
use App\Services\AI\GeminiService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;

/**
 * @property \Filament\Forms\Form $draftForm
 */
trait ManagesChatDraft
{
    public bool $isGeneratingDraft = false;
    public ?array $ratingResult = null;
    public bool $isRating = false;

    /**
     * Copies the active AI draft directly into the rich text composer
     */
    public function copyAiDraftToComposer(): void
    {
        if ($this->activeDraft) {
            $this->draftForm->fill([
                'body_html' => $this->activeDraft->body_html,
            ]);

            // Clear previous grade and reset composer border
            $this->ratingResult = null;
            $this->isRating = false;

            Notification::make()
                ->title('Draft copied to composer!')
                ->success()
                ->send();
        }
    }

    /**
     * Marks the active thread as unread
     */
    public function markAsUnread(): void
    {
        if ($this->activeThreadId) {
            $thread = Thread::find($this->activeThreadId);
            $thread?->update(['is_unread' => true]);

            $this->activeThreadId = null;
            $this->activeDraft = null;
            $this->draftForm->fill([]);

            Notification::make()->title('Conversation marked as unread')->success()->send();
        }
    }

    /**
     * Reactively clears stale evaluation scores whenever the user types or alters text
     */
    public function updatedDraftDataBodyHtml(): void
    {
        $this->ratingResult = null;
    }

    /**
     * Validates compliance & sends the outbound email
     */
    public function approveAndSend(): void
    {
        $thread = Thread::find($this->activeThreadId);

        if (! $thread) {
            return;
        }

        // 🛡️ SANDBOX GUARD: Block email dispatch on baseline threads for non-root users
        if (auth()->id() !== 1 && method_exists($thread, 'isBaselineRecord') && $thread->isBaselineRecord()) {
            Notification::make()
                ->warning()
                ->title('🛡️ Sandbox Protected Thread')
                ->body('Direct outbound email dispatch is restricted on baseline demo threads.')
                ->actions([
                    Action::make('simulate')
                        ->label('Simulate Inbound Lead')
                        ->button()
                        ->color('warning')
                        ->outlined()
                        ->dispatch('open-modal', ['id' => 'inbound-simulator-modal'])
                        ->close(),
                ])
                ->send();

            return;
        }

        // ⚖️ COMPLIANCE GATE: Dispatches event to trigger AI evaluation
        if (! auth()->user()->can('bypass_compliance_gate')) {
            if (! isset($this->ratingResult) || ! ($this->ratingResult['is_compliant'] ?? false)) {
                Notification::make()
                    ->title('Compliance Block')
                    ->body('This draft must be graded and certified as FHA-compliant before sending.')
                    ->danger()
                    ->actions([
                        Action::make('gradeDraft')
                            ->label('Grade Draft')
                            ->button()
                            ->color('info')
                            ->outlined()
                            ->icon('heroicon-o-shield-check')
                            ->dispatch('trigger-grade-draft')
                            ->close(),
                        Action::make('openAiInsights')
                            ->label('Use AI Suggested Draft')
                            ->button()
                            ->color('success')
                            ->outlined()
                            ->icon('heroicon-o-cpu-chip')
                            ->dispatch('open-ai-tab')
                            ->close(),
                    ])
                    ->send();

                return;
            }
        }

        $data = $this->draftForm->getState();

        if (empty($data['body_html'])) {
            return;
        }

        // Find the last inbound message so we can perfectly thread the reply
        $lastInbound = $thread->messages()->where('direction', MessageDirection::INBOUND)->latest()->first();

        $messageToSend = null;

        if ($this->activeDraft) {
            $this->activeDraft->update([
                'body_html' => $data['body_html'],
                'body_text' => strip_tags($data['body_html']),
                'is_draft' => false,
                'in_reply_to' => $lastInbound?->mailbox_message_id,
            ]);

            $messageToSend = $this->activeDraft;
        } else {
            $messageToSend = Message::create([
                'thread_id' => $thread->id,
                'client_id' => $thread->client_id,
                'user_id' => auth()->id(),
                'direction' => MessageDirection::OUTBOUND,
                'body_html' => $data['body_html'],
                'body_text' => strip_tags($data['body_html']),
                'is_draft' => false,
                'is_ai_generated' => false,
                'in_reply_to' => $lastInbound?->mailbox_message_id,
            ]);
        }

        SendOutboundEmailJob::dispatch($messageToSend);

        $thread->update(['last_message_at' => now()]);

        $this->activeThreadId = null;
        $this->activeDraft = null;
        $this->draftForm->fill([]);
        $this->ratingResult = null;
        $this->isRating = false;

        Notification::make()
            ->title('Reply Sent Successfully')
            ->success()
            ->send();
    }

    public function discardDraft(): void
    {
        if ($this->activeDraft) {
            $this->activeDraft->delete();
            $this->activeDraft = null;
            $this->draftForm->fill([]);

            $this->ratingResult = null;
            $this->isRating = false;

            Notification::make()->title('Draft Discarded')->info()->send();
        }
    }

    public function regenerateDraft(): void
    {
        $thread = Thread::find($this->activeThreadId);
        if (! $thread) return;

        if ($this->activeDraft) {
            $this->activeDraft->delete();
            $this->activeDraft = null;
            $this->draftForm->fill([]);
        }

        $this->draftUpdatedAt = null;
        $this->isGeneratingDraft = true;

        DraftAiResponseJob::dispatch($thread);

        Notification::make()
            ->title('AI is generating...')
            ->success()
            ->send();
    }

    public function checkDraftStatus(): void
    {
        if (! $this->isGeneratingDraft || ! $this->activeThreadId) return;

        $thread = Thread::find($this->activeThreadId);
        $draft = $thread->messages()->where('is_draft', true)->latest()->first();

        if ($draft) {
            if ($this->draftUpdatedAt && $draft->updated_at->toDateTimeString() === $this->draftUpdatedAt) {
                return;
            }

            $this->activeDraft = $draft;
            $this->isGeneratingDraft = false;
            $this->draftUpdatedAt = null;

            Notification::make()
                ->title('Draft Ready!')
                ->success()
                ->send();
        }
    }

    public function rateCurrentDraft(GeminiService $gemini): void
    {
        $this->isRating = true;

        $proposedDraft = $this->draftData['body_html'] ?? '';

        if (is_array($proposedDraft)) {
            $parts = [];
            $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($proposedDraft));
            foreach ($iterator as $leaf) {
                if (is_string($leaf) || is_numeric($leaf)) {
                    $parts[] = $leaf;
                }
            }
            $proposedDraft = implode(' ', $parts);
        }
        $proposedDraft = (string) $proposedDraft;

        if (empty(strip_tags($proposedDraft))) {
            $this->isRating = false;
            Notification::make()->title('Draft is empty')->warning()->send();
            return;
        }

        $thread = Thread::find($this->activeThreadId);
        if (! $thread) {
            $this->isRating = false;
            return;
        }

        $messages = $thread->messages()->where('is_draft', false)->orderBy('created_at', 'asc')->get();
        $threadHistory = $messages->map(function ($msg) {
            $sender = $msg->direction->value === 1 ? 'Client' : 'Agent';
            return "[{$sender}]: " . strip_tags($msg->body_text ?? $msg->body_html ?? '');
        })->implode("\n\n");

        try {
            $this->ratingResult = $gemini->evaluateDraftComplianceAndQuality($threadHistory, $proposedDraft);
            $this->isRating = false;

            Notification::make()
                ->title('Draft Graded')
                ->body('Click the evaluation score badge to view details.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            $this->isRating = false;
            Notification::make()->title('Rating Error')->body($e->getMessage())->danger()->send();
        }
    }
}
