<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContactMessageStatus: string implements HasLabel, HasColor
{
    case Unread = 'unread';
    case Read = 'read';
    case Resolved = 'resolved';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Unread => 'Unread',
            self::Read => 'Read',
            self::Resolved => 'Resolved',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Unread => 'danger',
            self::Read => 'warning',
            self::Resolved => 'success',
        };
    }
}
