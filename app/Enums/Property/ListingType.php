<?php

declare(strict_types=1);

namespace App\Enums\Property;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ListingType: int implements HasLabel, HasColor
{
    case SALE = 1;
    case RENT = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SALE => 'For Sale',
            self::RENT => 'For Rent',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SALE => 'success',
            self::RENT => 'info',
        };
    }

    public static function fromString(?string $value): ?self
    {
        return match (strtolower(trim($value ?? ''))) {
            'sale', 'buy', 'for sale', 'purchase' => self::SALE,
            'rent', 'lease', 'for rent' => self::RENT,
            default => null,
        };
    }
}
