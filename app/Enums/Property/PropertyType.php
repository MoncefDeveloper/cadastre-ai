<?php

declare(strict_types=1);

namespace App\Enums\Property;

use Filament\Support\Contracts\HasLabel;

enum PropertyType: int implements HasLabel
{
    case APARTMENT = 1;
    case VILLA = 2;
    case STUDIO = 3;
    case COMMERCIAL = 4;
    case LAND = 5;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::APARTMENT => 'Apartment',
            self::VILLA => 'Villa',
            self::STUDIO => 'Studio',
            self::COMMERCIAL => 'Commercial',
            self::LAND => 'Land',
        };
    }
    public static function fromString(?string $value): ?self
    {
        return match (strtolower(trim($value ?? ''))) {
            'apartment', 'flat', 'condo' => self::APARTMENT,
            'villa', 'house', 'mansion' => self::VILLA,
            'studio' => self::STUDIO,
            'commercial', 'office', 'retail' => self::COMMERCIAL,
            'land', 'plot', 'lot' => self::LAND,
            default => null,
        };
    }
}
