<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Contact;
use App\Models\User;
use Filament\Notifications\Notification;

class ContactObserver
{
    /**
     * Ensure the notification is ONLY sent after the database transaction successfully commits.
     */
    public bool $afterCommit = true;

    public function created(Contact $contact): void
    {
        // Fetch users who should receive this notification (e.g., active admins)
        // Adjust this query if you only want to notify users with a specific Spatie role.
        $admins = User::where('is_active', true)->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::make()
            ->title("New Contact: {$contact->name}")
            ->body(str($contact->message)->limit(60)->toString())
            ->icon('heroicon-o-envelope')
            ->info()
            // Sends the notification to the Filament Database channel
            ->sendToDatabase($admins);
    }
}
