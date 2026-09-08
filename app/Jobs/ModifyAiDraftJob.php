<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Message;
use App\Services\AI\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ModifyAiDraftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;
    public int $tries = 3;
    public array $backoff = [10, 30, 60];
    public int $timeout = 60;

    public function __construct(
        public readonly Message $draft,
        public readonly string $instruction
    ) {}

    public function handle(GeminiService $gemini): void
    {
        Log::info('[AI Modifier] Modifying draft.', ['draft_id' => $this->draft->id, 'instruction' => $this->instruction]);

        $newHtml = $gemini->modifyDraft(
            currentDraftHtml: $this->draft->body_html,
            modifierInstruction: $this->instruction
        );

        // Update the existing draft.
        // This automatically bumps the 'updated_at' timestamp,
        // which signals our Livewire UI that the modification is complete.
        $this->draft->update([
            'body_html' => $newHtml,
            'body_text' => strip_tags($newHtml),
        ]);

        Log::info('[AI Modifier] Draft modified successfully!', ['draft_id' => $this->draft->id]);
    }
}
