<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Message;
use App\Services\Email\PostmarkOutboundService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOutboundEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];
    public int $timeout = 30;

    public function __construct(
        public readonly Message $message
    ) {}

    public function handle(): void
    {
        $thread = $this->message->thread;
        $client = $thread?->client;

        if (! $thread || ! $client) {
            return;
        }

        // Ensure the thread has a unique Mailbox Hash for Inbound routing
        if (! $thread->mailbox_hash) {
            $hash = 'th_' . uniqid();
            $thread->update(['mailbox_hash' => $hash]);
        }

        // 🛡️ REPUTATION GUARD: Intercept fake domains to protect Postmark deliverability score
        $recipientEmail = strtolower($client->email ?? '');
        $isMockDomain = str_ends_with($recipientEmail, '.test')
            || str_ends_with($recipientEmail, '.example')
            || str_ends_with($recipientEmail, '.invalid')
            || str_ends_with($recipientEmail, '.localhost');

        if ($isMockDomain) {
            Log::info("[SendOutboundEmailJob] Simulated delivery for mock domain: {$recipientEmail}");

            $this->message->update([
                'mailbox_message_id' => 'sim_out_' . uniqid(),
                'delivery_status' => 'delivered',
            ]);

            return;
        }

        // 🚀 Live Outbound Dispatch for Real Emails
        $outboundService = PostmarkOutboundService::make();

        $postmarkMessageId = $outboundService->sendEmail(
            to: $client->email,
            subject: $thread->subject ?? 'Re: Your Inquiry',
            htmlBody: $this->message->body_html ?? '',
            textBody: $this->message->body_text ?? '',
            inReplyToMessageId: $this->message->in_reply_to,
            threadHash: $thread->mailbox_hash
        );

        $this->message->update([
            'mailbox_message_id' => $postmarkMessageId,
            'delivery_status' => 'delivered',
        ]);
    }
}
