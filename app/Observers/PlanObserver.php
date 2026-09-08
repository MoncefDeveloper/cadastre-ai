<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Plan;
use Illuminate\Support\Facades\Cache;

class PlanObserver
{
    /**
     * Ensure background tasks and cache invalidation ONLY happen
     * after the database transaction successfully commits.
     */
    public bool $afterCommit = true;

    public function created(Plan $plan): void
    {
        $this->flushCache();
    }

    public function updated(Plan $plan): void
    {
        $this->flushCache();
    }

    public function deleted(Plan $plan): void
    {
        $this->flushCache();
    }

    private function flushCache(): void
    {
        // Invalidates the front-end query cache for pricing pages
        Cache::forget('saas_active_plans');
    }
}
