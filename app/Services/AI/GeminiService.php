<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Enums\Message\MessageDirection;
use App\Models\Message;
use App\Models\Thread;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class GeminiService
{
    private const MODEL = 'gemini-3.5-flash-lite';
    private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/';

    private function getApiKey(): string
    {
        $keys = array_filter(config('services.gemini.api_keys', []));
        if (empty($keys)) {
            throw new Exception("No Gemini API keys configured.");
        }
        return Arr::random($keys);
    }

    public function extractCriteria(Message $message): array
    {
        $contextMessages = $message->thread->messages()
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->reverse()
            ->map(fn($m) => ($m->direction === MessageDirection::INBOUND ? 'Client: ' : 'Agent/AI: ') . $m->body_text)
            ->implode("\n\n");

        $systemInstruction = <<<TEXT
You are an elite Real Estate AI Assistant.
Analyze the email conversation context and extract the client's property search criteria.

Return STRICTLY a JSON object matching this schema. NO markdown backticks.
{
    "is_property_inquiry": boolean,
    "listing_type": string|null,
    "property_type": string|null,
    "city": string|null,
    "budget_max": integer|null,
    "bedrooms": integer|null,
    "bathrooms": integer|null
}
TEXT;

        $response = Http::withoutVerifying()->timeout(30)
            ->post(self::API_URL . self::MODEL . ':generateContent?key=' . $this->getApiKey(), [
                'systemInstruction' => ['parts' => [['text' => $systemInstruction]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => $contextMessages]]]],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'thinkingConfig' => ['thinkingLevel' => 'high']
                ],
            ]);

        if ($response->status() === 429) {
            throw new Exception("Gemini Rate Limit Exceeded (429). Retrying via Queue.");
        }

        if (!$response->successful()) {
            throw new Exception("Gemini API Error: " . $response->body());
        }

        $jsonString = $response->json('candidates.0.content.parts.0.text') ?? '{}';
        $jsonString = str_replace(['```json', '```'], '', $jsonString);

        return json_decode(trim($jsonString), true) ?? [];
    }

    public function generateDraft(Thread $thread, string $systemInstructions, string $prompt, array $properties): string
    {
        $contextMessages = $thread->messages()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->reverse()
            ->map(fn($m) => ($m->direction === MessageDirection::INBOUND ? 'Client: ' : 'Agent/AI: ') . $m->body_text)
            ->implode("\n\n");

        $propertyContext = json_encode(array_map(fn($p) => [
            'title' => $p['title'],
            'price' => '$' . number_format($p['price'] / 100, 2),
            'city' => $p['city'],
            'specs' => "{$p['bedrooms']} Beds, {$p['bathrooms']} Baths",
            // 👈 Updated to Cadastre domain
            'link' => rtrim(config('app.url', 'https://cadastre.ai'), '/') . "/properties/{$p['slug']}"
        ], $properties), JSON_PRETTY_PRINT);

        // Strict HTML rules injected into system instructions
        $enforcedInstructions = $systemInstructions . "\n\nCRITICAL RULE: Return ONLY pure, semantic HTML. Allowed tags: <p>, <br>, <strong>, <em>, <ul>, <ol>, <li>, <a>. Do NOT use inline CSS (no style=\"...\"). Do NOT use markdown code blocks (```html). Just the raw HTML elements.";

        $userPrompt = "Thread History:\n" . $contextMessages . "\n\n" .
            "Template Prompt:\n" . $prompt . "\n\n" .
            "Properties to Pitch:\n" . $propertyContext;

        $response = Http::withoutVerifying()->timeout(45)
            ->post(self::API_URL . self::MODEL . ':generateContent?key=' . $this->getApiKey(), [
                'systemInstruction' => ['parts' => [['text' => $enforcedInstructions]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => $userPrompt]]]],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'thinkingConfig' => ['thinkingLevel' => 'high']
                ],
            ]);

        if (!$response->successful()) {
            throw new Exception("Gemini AI Draft Error: " . $response->body());
        }

        $rawDraft = $response->json('candidates.0.content.parts.0.text') ?? '';
        return $this->cleanHtmlOutput($rawDraft);
    }

    public function modifyDraft(string $currentDraftHtml, string $modifierInstruction): string
    {
        $systemInstruction = "You are an elite real estate copywriter. Modify the provided HTML email draft exactly as the user instructs. CRITICAL RULES: 1) Return ONLY pure, semantic HTML. 2) Maintain existing links. 3) DO NOT change the language of the original draft unless explicitly told to translate it.";

        $userPrompt = "Modification Instruction: {$modifierInstruction}\n\nCurrent Draft:\n{$currentDraftHtml}";

        $response = Http::withoutVerifying()->timeout(30)
            ->post(self::API_URL . self::MODEL . ':generateContent?key=' . $this->getApiKey(), [
                'systemInstruction' => ['parts' => [['text' => $systemInstruction]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => $userPrompt]]]],
                'generationConfig' => [
                    'temperature' => 0.5,
                    'thinkingConfig' => ['thinkingLevel' => 'high']
                ],
            ]);

        if (!$response->successful()) {
            throw new Exception("Gemini Modification Error: " . $response->body());
        }

        $rawDraft = $response->json('candidates.0.content.parts.0.text') ?? '';
        return $this->cleanHtmlOutput($rawDraft);
    }

    private function cleanHtmlOutput(string $html): string
    {
        $html = trim(str_replace(['```html', '```'], '', $html));
        $html = preg_replace('/(style|class)="[^"]*"/i', '', $html);

        return trim($html);
    }

    public function evaluateDraftComplianceAndQuality(string $threadHistory, string $proposedDraft): array
    {
        // 👈 Updated to cadastre-rules config
        $rulesConfig = config('cadastre-rules', []);
        $compiledRules = '';

        foreach ($rulesConfig as $section) {
            $compiledRules .= "### {$section['title']}\n";
            foreach ($section['rules'] as $rule) {
                $compiledRules .= "- **{$rule['name']}**: {$rule['action']}\n";
            }
            $compiledRules .= "\n";
        }

        $systemInstruction = <<<TEXT
You are an elite real estate compliance officer and copy editor.
Analyze the provided email thread history and the proposed draft response.

Evaluate the draft against these strict regulatory and quality rules:

{$compiledRules}

CRITICAL CONFLICT ESCAPE CLAUSE:
If the client explicitly asks a subjective question in the thread history (e.g., asking if a street is "safe" or "quiet"), the agent is permitted to answer. However, the agent must quickly pivot the answer to objective, physical, or spatial facts (e.g., "low vehicular traffic," "cul-de-sac," "triple-pane window insulation"). Do not flag these direct answers as non-compliant if they successfully anchor to objective facts.

Return strictly a JSON object matching this schema. Do NOT wrap in markdown backticks.
{
  "is_compliant": boolean,
  "compliance_warning": "string or null",
  "grade": "string (A, B, C, D, or F)",
  "score": integer (1 to 10),
  "critique": "string (under 3 sentences)",
  "summary": "string (under 400 characters detailing why this grade was awarded)"
}
TEXT;

        $userPrompt = "THREAD HISTORY:\n{$threadHistory}\n\nPROPOSED DRAFT:\n{$proposedDraft}";

        $response = Http::withoutVerifying()->timeout(30)
            ->post(self::API_URL . self::MODEL . ':generateContent?key=' . $this->getApiKey(), [
                'systemInstruction' => ['parts' => [['text' => $systemInstruction]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => $userPrompt]]]],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'temperature' => 0.1,
                ],
            ]);

        if (!$response->successful()) {
            throw new Exception("Gemini Compliance Error: " . $response->body());
        }

        $jsonString = $response->json('candidates.0.content.parts.0.text') ?? '{}';
        $jsonString = str_replace(['```json', '```'], '', $jsonString);

        return json_decode(trim($jsonString), true) ?? [
            'is_compliant' => true,
            'compliance_warning' => null,
            'grade' => 'F',
            'score' => 0,
            'critique' => 'Parsing error occurred.',
            'summary' => 'The AI failed to return a valid structured evaluation.'
        ];
    }
}
