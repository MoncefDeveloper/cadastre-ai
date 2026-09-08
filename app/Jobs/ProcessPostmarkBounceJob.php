<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Message;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPostmarkBounceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public readonly array $payload
    ) {}

    public function handle(): void
    {
        $messageId = $this->payload['MessageID'] ?? null;
        $bounceType = $this->payload['Type'] ?? 'Unknown Bounce';
        $email = $this->payload['Email'] ?? 'Unknown Email';

        if (!$messageId) {
            return;
        }

        // Eager load the thread and its assignee to optimize queries
        $message = Message::with('thread.assignee')->where('mailbox_message_id', $messageId)->first();

        if (!$message) {
            return;
        }

        $message->update([
            'delivery_status' => 'bounced',
            'bounced_at' => now(),
        ]);

        Log::warning("[Postmark Bounce] MessageID: {$messageId} failed. Type: {$bounceType}");

        // --- NOTIFICATION ROUTING (Rule A) ---
        $recipients = collect();

        if ($message->thread && $message->thread->assigned_user_id) {
            $agent = $message->thread->assignee;
            if ($agent) $recipients->push($agent);
        } else {
            $recipients = User::role(['super_admin', 'Admin'])->get();
        }

        // Dispatch Critical Filament DB Notification
        foreach ($recipients as $user) {
            FilamentNotification::make()
                ->title("Email Bounce Alert: {$email}")
                ->body("Delivery failed. Reason: {$bounceType}")
                ->icon('heroicon-o-exclamation-triangle')
                ->danger() // Renders as red/critical alert in UI
                ->sendToDatabase($user, isEventDispatched: true);
            // isEventDispatched: true works alongside our polling/Reverb logic
        }
    }
}
