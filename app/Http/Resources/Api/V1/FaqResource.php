<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Faq
 */
class FaqResource extends JsonResource
{
    /**
     * Transform the Faq model into a sanitized payload for Framer accordion components.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => (int) $this->id,
            'question'   => (string) $this->question,
            'answer'     => $this->sanitizeHtml((string) $this->answer),
            'sort_order' => (int) $this->sort_order,
        ];
    }

    /**
     * Defensive XSS sanitization:
     * Retains semantic rich text tags (<p>, <strong>, <em>, <a>, <code>, <ul>, <ol>, <li>)
     * while stripping executable scripts, iframe injections, and malicious event handlers.
     */
    private function sanitizeHtml(string $html): string
    {
        // Obliterate script, iframe, object, and embed blocks along with their inner contents
        $cleaned = preg_replace('/<(script|iframe|object|embed)\b[^>]*>(.*?)<\/\1>/is', '', $html) ?? $html;

        // Remove any self-closing or malformed script/iframe tags
        $cleaned = preg_replace('/<(script|iframe|object|embed)\b[^>]*\/?>/is', '', $cleaned) ?? $cleaned;

        // Strips on* attributes whether quoted (single/double) or unquoted
        $cleaned = preg_replace('/\son\w+\s*=\s*(?:["\'][^"\']*["\']|[^\s>]+)/is', '', $cleaned) ?? $cleaned;

        // Neutralize javascript: pseudo-protocols in href and src attributes
        $cleaned = preg_replace('/(href|src)\s*=\s*(["\']?)\s*javascript:[^"\'>]*\2/is', '$1="#"', $cleaned) ?? $cleaned;

        return trim($cleaned);
    }
}
