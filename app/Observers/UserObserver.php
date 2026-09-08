<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\NotificationType;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Auto-seed default preferences for new agents
        $user->notificationPreferences()->create([
            'notification_type' => NotificationType::NEW_MESSAGE,
            'channel_database' => true,
            'channel_mail' => false,
            'channel_sms' => false,
            'channel_whatsapp' => false,
        ]);
    }
}
