<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\Message\MessageDirection;
use App\Jobs\AnalyzeClientIntentJob;
use App\Models\Message;

class MessageObserver
{
    /**
     * Enforce transactional integrity. Only dispatch after DB commit.
     */
    public bool $afterCommit = true;

    public function created(Message $message): void
    {
        // Only trigger the AI intent pipeline for raw Inbound client messages
        if ($message->direction === MessageDirection::INBOUND && !$message->is_ai_generated) {
            AnalyzeClientIntentJob::dispatch($message);
        }
    }
}
