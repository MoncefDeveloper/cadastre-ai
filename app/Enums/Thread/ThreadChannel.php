<?php

declare(strict_types=1);

namespace App\Enums\Thread;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ThreadChannel: int implements HasLabel, HasColor
{
    case EMAIL = 1;
    case WHATSAPP = 2;
    case WEBFORM = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EMAIL => 'Email',
            self::WHATSAPP => 'WhatsApp',
            self::WEBFORM => 'Webform',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::EMAIL => 'info',
            self::WHATSAPP => 'success',
            self::WEBFORM => 'warning',
        };
    }
}
