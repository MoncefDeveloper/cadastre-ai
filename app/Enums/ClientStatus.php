<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ClientStatus: int implements HasLabel, HasColor
{
    case NEW = 1;
    case ACTIVE = 2;
    case CLOSED = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NEW => 'New Lead',
            self::ACTIVE => 'Active',
            self::CLOSED => 'Closed',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NEW => 'info',
            self::ACTIVE => 'success',
            self::CLOSED => 'gray',
        };
    }
}
