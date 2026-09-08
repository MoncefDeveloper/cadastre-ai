<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'notification_type',
    'channel_database',
    'channel_mail',
    'channel_sms',
    'channel_whatsapp'
])]
class NotificationPreference extends Model
{
    protected function casts(): array
    {
        return [
            'notification_type' => NotificationType::class,
            'channel_database' => 'boolean',
            'channel_mail' => 'boolean',
            'channel_sms' => 'boolean',
            'channel_whatsapp' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
