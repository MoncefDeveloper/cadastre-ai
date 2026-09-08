<?php

declare(strict_types=1);

namespace App\Jobs;

use App\DTOs\Email\PostmarkInboundDTO;
use App\Enums\Message\MessageDirection;
use App\Enums\Thread\ThreadChannel;
use App\Models\Client;
use App\Models\Message;
use App\Models\Thread;
use App\Models\User;
use App\Notifications\NewInboundMessageNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class ProcessInboundEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public readonly PostmarkInboundDTO $emailData
    ) {}

    public function handle(): void
    {
        $rfcMessageId = $this->emailData->getRfcMessageId();

        if (Message::where('mailbox_message_id', $rfcMessageId)->exists()) {
            return;
        }

        DB::transaction(function () use ($rfcMessageId) {
            $client = Client::firstOrCreate(
                ['email' => $this->emailData->fromEmail],
                [
                    'first_name' => $this->emailData->fromName ?: 'Unknown',
                    'source' => 'Postmark Inbound',
                ]
            );

            $thread = $this->findOrRouteThread($client);

            // LAYER 1: Sanitize Inbound HTML Payload
            $cleanHtml = $this->extractHtmlBodyContent($this->emailData->htmlBody);

            $message = Message::create([
                'thread_id' => $thread->id,
                'client_id' => $client->id,
                'mailbox_message_id' => $rfcMessageId,
                'in_reply_to' => $this->emailData->getInReplyToHeader(),
                'direction' => MessageDirection::INBOUND,
                'body_text' => $this->emailData->textBody,
                'body_html' => $cleanHtml, // <-- Safely assigned
                'attachments' => $this->emailData->attachments,
            ]);

            $thread->update([
                'last_message_at' => now(),
                'is_unread' => true,
            ]);

            DB::afterCommit(function () use ($thread, $message) {
                $existingRoles = Role::whereIn('name', ['super_admin', 'Admin'])
                    ->where('guard_name', 'web')
                    ->pluck('name')
                    ->toArray();

                $recipients = Collection::empty();

                if ($thread->assigned_user_id) {
                    $agent = $thread->assignee;
                    if ($agent) $recipients->push($agent);
                } elseif (!empty($existingRoles)) {
                    $recipients = User::role($existingRoles)->get();
                } else {
                    $recipients = User::where('is_active', true)->get();
                }

                $recipients->load('notificationPreferences');

                foreach ($recipients as $user) {
                    $user->notify(new NewInboundMessageNotification($message));
                }
            });

            AnalyzeClientIntentJob::dispatch($message);
        });
    }

    private function findOrRouteThread(Client $client): Thread
    {
        if ($this->emailData->mailboxHash) {
            $thread = Thread::where('mailbox_hash', $this->emailData->mailboxHash)->first();
            if ($thread) return $thread;
        }

        $inReplyTo = $this->emailData->getInReplyToHeader();
        if ($inReplyTo) {
            $cleanId = explode('@', $inReplyTo)[0];
            $parentMessage = Message::where('mailbox_message_id', 'LIKE', $cleanId . '%')->first();
            if ($parentMessage) return $parentMessage->thread;
        }

        return Thread::create([
            'client_id' => $client->id,
            'assigned_user_id' => $this->getDefaultAssigneeId(),
            'subject' => $this->emailData->subject,
            'channel' => ThreadChannel::EMAIL,
            'last_message_at' => now(),
            'mailbox_hash' => $this->emailData->mailboxHash ?? ('th_' . uniqid()),
        ]);
    }

    /**
     * Layer 1 Guard: Extracts pure semantic body, destroying injected <style> headers.
     */
    private function extractHtmlBodyContent(string $rawHtml): string
    {
        if (empty(trim($rawHtml))) {
            return '';
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();

        // Force UTF-8 encoding to prevent accents from breaking (e.g. French Riviera)
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $rawHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $body = $dom->getElementsByTagName('body')->item(0);

        if (!$body) {
            // Fallback if no <body> tag exists: aggressively regex out styles
            return preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $rawHtml);
        }

        // Obliterate any illegally nested <style> blocks inside the body
        $styles = $body->getElementsByTagName('style');
        while ($styles->length > 0) {
            $style = $styles->item(0);
            $style->parentNode->removeChild($style);
        }

        $cleanHtml = '';
        foreach ($body->childNodes as $child) {
            $cleanHtml .= $dom->saveHTML($child);
        }

        return trim($cleanHtml);
    }

    private function getDefaultAssigneeId(): ?int
    {
        // 1. Assign to the active Senior Agent (User ID 4: Agent Demo)
        $agent = User::role('Senior Agent') // 👈 Exact DB match
            ->where('id', '!=', 1)
            ->where('is_active', true)
            ->first();

        if ($agent) {
            return $agent->id;
        }

        // 2. Fallback to Broker Manager or Admin
        $operationalLead = User::role(['Broker Manager', 'Admin']) // 👈 Exact DB match
            ->where('id', '!=', 1)
            ->where('is_active', true)
            ->first();

        if ($operationalLead) {
            return $operationalLead->id;
        }

        // 3. Fallback to any active non-root user
        $fallbackUser = User::where('id', '!=', 1)
            ->where('is_active', true)
            ->first();

        return $fallbackUser?->id;
    }
}
