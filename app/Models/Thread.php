<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Thread\ThreadChannel;
use App\Enums\Thread\ThreadPriority;
use App\Enums\Thread\ThreadStatus;
use App\Models\Concerns\ProtectsBaseline;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'client_id',
    'assigned_user_id',
    'mailbox_hash',
    'subject',
    'status',
    'priority',
    'channel',
    'is_unread',
    'snoozed_until',
    'extracted_criteria',
    'last_message_at'
])]
class Thread extends Model
{
    use HasFactory, ProtectsBaseline;

    protected function casts(): array
    {
        return [
            'status' => ThreadStatus::class,
            'priority' => ThreadPriority::class,
            'channel' => ThreadChannel::class,
            'is_unread' => 'boolean',
            'snoozed_until' => 'datetime',
            'last_message_at' => 'datetime',
            'extracted_criteria' => 'array', // Automatically casts JSON column to PHP Array
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function propertyMatches(): HasMany
    {
        return $this->hasMany(ThreadPropertyMatch::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
