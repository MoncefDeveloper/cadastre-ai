<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Enums\ContactMessageStatus;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;

class ContactService
{
    /**
     * Ingest an advisory lead inquiry, validating anti-spam honeypots before persisting.
     *
     * @param array<string, mixed> $data
     */
    public function storeContact(array $data, ?string $ipAddress): bool
    {
        // 1. Zero-Write Honeypot Trap
        // If a bot fills the hidden honeypot input, simulate success without writing to the database
        if (! empty($data['_cadastre_hp'])) {
            Log::warning('[Honeypot Intercepted] Automated bot lead submission dropped.', [
                'ip'    => $ipAddress,
                'email' => $data['email'] ?? 'unknown',
            ]);

            return true;
        }

        // 2. Persist to MySQL / SQLite
        // Model creation automatically invokes ContactObserver::created() ($afterCommit = true),
        // dispatching in-app database notifications to all active Filament administrators
        Contact::create([
            'name'       => (string) $data['name'],
            'email'      => (string) $data['email'],
            'phone'      => $data['phone'] ?? null,
            'subject'    => (string) $data['subject'],
            'message'    => (string) $data['message'],
            'ip_address' => $ipAddress,
            'status'     => ContactMessageStatus::Unread,
        ]);

        return true;
    }
}
