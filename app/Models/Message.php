<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Message\MessageDirection;
use App\Models\Concerns\ProtectsBaseline;
use App\Observers\MessageObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([MessageObserver::class])]
#[Fillable([
    'thread_id',
    'user_id',
    'template_id',
    'client_id',
    'mailbox_message_id',
    'in_reply_to',
    'direction',
    'body_text',
    'body_html',
    'is_draft',
    'is_ai_generated',
    'attachments',
    'delivery_status', // <-- ADD THIS
    'bounced_at'
])]
class Message extends Model
{
    use HasFactory, ProtectsBaseline;

    protected function casts(): array
    {
        return [
            'direction' => MessageDirection::class,
            'is_draft' => 'boolean',
            'is_ai_generated' => 'boolean',
            'attachments' => 'array',
        ];
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function senderClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
