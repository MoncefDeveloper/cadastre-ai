<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Faq;
use Illuminate\Support\Facades\Cache;

class FaqObserver
{
    /**
     * Ensure cache invalidation ONLY happens after the database transaction successfully commits.
     */
    public bool $afterCommit = true;

    public function created(Faq $faq): void
    {
        $this->flushCache();
    }

    public function updated(Faq $faq): void
    {
        $this->flushCache();
    }

    public function deleted(Faq $faq): void
    {
        $this->flushCache();
    }

    private function flushCache(): void
    {
        // Invalidates the front-end query cache for all FAQ sections
        Cache::forget('saas_active_faqs');
    }
}
