<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\Message\MessageDirection;
use App\Models\Message;
use App\Models\Template;
use App\Models\Thread;
use App\Models\User;
use App\Services\AI\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ApplyTemplateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;
    public int $tries = 3;
    public array $backoff = [10, 30, 60];
    public int $timeout = 60;

    public function __construct(
        public readonly Thread $thread,
        public readonly Template $template,
        public readonly ?User $user = null // Accept the user who triggered it
    ) {}

    public function handle(GeminiService $gemini): void
    {
        $matchedProperties = $this->thread->propertyMatches()
            ->with('property')
            ->where('is_rejected', false)
            ->get()
            ->pluck('property')
            ->toArray();

        // DUPLICATE PREVENTION
        Message::where('thread_id', $this->thread->id)->where('is_draft', true)->delete();

        Log::info('[AI Template] Generating draft via Template.', ['template_id' => $this->template->id]);

        $htmlBody = $gemini->generateDraft(
            thread: $this->thread,
            systemInstructions: $this->template->system_instructions ?? 'You are an elite real estate agent.',
            prompt: $this->template->prompt,
            properties: $matchedProperties
        );

        $message = Message::create([
            'thread_id' => $this->thread->id,
            'client_id' => $this->thread->client_id,
            'user_id' => $this->user?->id,
            'template_id' => $this->template->id,
            'direction' => \App\Enums\Message\MessageDirection::OUTBOUND,
            'body_html' => $htmlBody,
            'body_text' => strip_tags($htmlBody),
            'is_draft' => true,
            'is_ai_generated' => true,
        ]);

        Log::info('[Template Verification] Template Draft Successfully Generated', [
            'new_draft_id' => $message->id,
        ]);
    }
}
