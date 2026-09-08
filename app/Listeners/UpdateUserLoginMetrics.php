<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Events\Attributes\AsListener;

#[AsListener(event: Login::class)]
class UpdateUserLoginMetrics
{
    public function handle(Login $event): void
    {
        $event->user->withoutTimestamps(function () use ($event): void {
            $event->user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
        });
    }
}
