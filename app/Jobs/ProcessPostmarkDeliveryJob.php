<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPostmarkDeliveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public readonly array $payload
    ) {}

    public function handle(): void
    {
        $messageId = $this->payload['MessageID'] ?? null;

        if (!$messageId) {
            return;
        }

        // Postmark MessageIDs are mapped to our mailbox_message_id column during outbound dispatch
        $message = Message::where('mailbox_message_id', $messageId)->first();

        $message?->update([
            'delivery_status' => 'delivered',
        ]);

        Log::info("[Postmark Delivery] Confirmed for MessageID: {$messageId}");
    }
}
