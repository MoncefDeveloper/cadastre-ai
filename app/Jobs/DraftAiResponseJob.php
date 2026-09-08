<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\Message\MessageDirection;
use App\Models\Message;
use App\Models\Thread;
use App\Services\AI\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DraftAiResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;
    public int $tries = 3;
    public array $backoff = [10, 30, 60];
    public int $timeout = 60;

    public function __construct(
        public readonly Thread $thread
    ) {}

    public function handle(GeminiService $gemini): void
    {
        $matchedProperties = $this->thread->propertyMatches()
            ->with('property')
            ->where('is_rejected', false)
            ->get()
            ->pluck('property')
            ->toArray();

        // 1. DUPLICATE PREVENTION: Delete any existing drafts for this thread before creating a new one.
        Message::where('thread_id', $this->thread->id)->where('is_draft', true)->delete();

        // 2. EXISTENTIAL PERSONA CHECK: Decoupled from manual CRM statuses.
        // Check if any other thread exists for this client that was created BEFORE the current thread.
        $hasPriorInteractions = Thread::where('client_id', $this->thread->client_id)
            ->where('id', '<', $this->thread->id)
            ->exists();

        $isNewClient = ! $hasPriorInteractions;

        // 3. DYNAMIC PERSONA INSTRUCTION
        $personaInstruction = $isNewClient
            ? "CLIENT RELATIONSHIP: FIRST-TIME INQUIRY.\n- You must include a warm, highly professional opening sentence introducing the client to 'MatchMaker Agency'.\n- Introduce our mission briefly before addressing their property preferences."
            : "CLIENT RELATIONSHIP: RETURNING CLIENT.\n- DO NOT introduce 'MatchMaker Agency'.\n- DO NOT use introductory onboarding formulas.\n- Treat this as an ongoing business relationship. Transition immediately to their property query.";

        // 4. CONTEXT-AWARE SYSTEM INSTRUCTIONS
        $systemInstructions = "You are an elite, highly professional Real Estate Agent. Your goal is to be helpful, persuasive, and politely luxurious. ALWAYS reply in the exact same language the client used in their latest message.";

        // 5. SMART PROMPT: Combine persona state with the core drafting logic.
        $prompt = <<<TEXT
{$personaInstruction}

Analyze the Thread History.
If this is a NEW inquiry: Acknowledge their requirements and pitch the provided properties elegantly (with native HTML <a> links). If no properties match, ask about flexibility.
If this is an ONGOING conversation (e.g. the client replied "perfect", "yes", "let's view it"): Acknowledge their reply naturally and propose the next logical step (e.g., proposing dates for a viewing, asking for phone number). Do NOT repitch the properties if they already agreed.
Keep the tone concise, professional, warm, and highly personalized.
TEXT;

        Log::info('[AI Draft] Requesting Gemini to draft default email.', [
            'thread_id' => $this->thread->id,
            'is_new_client' => $isNewClient
        ]);

        $htmlBody = $gemini->generateDraft(
            thread: $this->thread,
            systemInstructions: $systemInstructions,
            prompt: $prompt,
            properties: $matchedProperties
        );

        $message = Message::create([
            'thread_id' => $this->thread->id,
            'client_id' => $this->thread->client_id,
            'template_id' => null,
            'direction' => MessageDirection::OUTBOUND,
            'body_html' => $htmlBody,
            'body_text' => strip_tags($htmlBody),
            'is_draft' => true,
            'is_ai_generated' => true,
        ]);

        Log::info('[AI Draft] Default Draft created successfully!', ['message_id' => $message->id]);
    }
}
