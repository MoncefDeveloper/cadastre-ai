<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContactMessageStatus;
use App\Models\Concerns\ProtectsBaseline;
use App\Observers\ContactObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ContactObserver::class])]
#[Fillable([
    'name',
    'email',
    'phone',
    'subject',
    'message',
    'ip_address',
    'status',
])]
class Contact extends Model
{
    use HasFactory, ProtectsBaseline;

    protected function casts(): array
    {
        return [
            'status' => ContactMessageStatus::class,
        ];
    }
}
