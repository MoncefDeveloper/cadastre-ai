<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Message;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInboundMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Message $message
    ) {}

    public function via(object $notifiable): array
    {
        $channels = [];

        // 1. IN-MEMORY FILTERING: Prevents N+1 by checking the already hydrated collection
        $preference = $notifiable->notificationPreferences
            ->firstWhere('notification_type', NotificationType::NEW_MESSAGE);

        // 2. FALLBACK: If no preference exists (e.g., existing unseeded admin), default to both
        if (!$preference) {
            return ['database', 'mail'];
        }

        // 3. DYNAMIC ROUTING: Build array based on user toggles
        if ($preference->channel_database) {
            $channels[] = 'database';
        }

        if ($preference->channel_mail) {
            $channels[] = 'mail';
        }

        return $channels;
    }


    public function toDatabase(object $notifiable): array
    {
        $clientName = $this->message->senderClient->first_name ?? 'Client';

        return FilamentNotification::make()
            ->title("New Reply from {$clientName}")
            ->body(str($this->message->body_text)->limit(60)->toString())
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->info()
            ->getDatabaseMessage();
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clientName = $this->message->senderClient->first_name ?? 'Client';
        $clientFullName = trim(($this->message->senderClient->first_name ?? '') . ' ' . ($this->message->senderClient->last_name ?? ''));
        $snippet = str($this->message->body_text)->limit(150);
        $url = route('filament.admin.pages.inbox'); // Translates to /admin/inbox natively

        return (new MailMessage)
            ->mailer('namecheap') // STRICTLY force Namecheap SMTP to save Postmark credits
            ->subject("MatchMaker Alert: New Message from {$clientName}")
            ->view('emails.new-message-alert', [
                'agentName' => $notifiable->name,
                'clientName' => $clientFullName ?: 'A client',
                'snippet' => $snippet,
                'url' => $url,
            ]);
    }
}
