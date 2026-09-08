<?php

declare(strict_types=1);

namespace App\Enums\Property;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PropertyStatus: int implements HasLabel, HasColor
{
    case AVAILABLE = 1;
    case UNDER_OFFER = 2;
    case SOLD = 3;
    case RENTED = 4;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::UNDER_OFFER => 'Under Offer',
            self::SOLD => 'Sold',
            self::RENTED => 'Rented',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::UNDER_OFFER => 'warning',
            self::SOLD => 'danger',
            self::RENTED => 'gray',
        };
    }
}
