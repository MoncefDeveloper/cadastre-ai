<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Http\Resources\Api\V1\PlanResource;
use App\Models\Plan;
use Illuminate\Support\Facades\Cache;

class PlanService
{
    /**
     * SSOT cache key strictly matching PlanObserver::flushCache().
     */
    public const CACHE_KEY = 'saas_active_plans';

    /**
     * Retrieve and cache active subscription packages for Framer pricing tables.
     * Caches plain resolved arrays to guarantee compatibility with config/cache.php
     * 'serializable_classes' => false guard.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getActivePlans(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            $plans = Plan::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc') // Fallback prevents database-specific randomized ordering
                ->get();

            return PlanResource::collection($plans)->resolve();
        });
    }
}
