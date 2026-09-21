<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Http\Resources\Api\V1\FaqResource;
use App\Models\Faq;
use Illuminate\Support\Facades\Cache;

class FaqService
{
    /**
     * SSOT cache key strictly matching FaqObserver::flushCache().
     */
    public const CACHE_KEY = 'saas_active_faqs';

    /**
     * Retrieve and cache active global FAQs for Framer accordion components.
     * Caches plain resolved arrays to remain 100% compliant with config/cache.php
     * 'serializable_classes' => false guard.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getActiveFaqs(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            $faqs = Faq::where('is_active', true)
                ->where('target_audience', 'global') // Restricts internal agent/billing FAQs from public view
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc') // Deterministic secondary sorting
                ->get();

            return FaqResource::collection($faqs)->resolve();
        });
    }
}
