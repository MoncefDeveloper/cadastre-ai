<?php

declare(strict_types=1);

namespace App\Enums\Category;

use Filament\Support\Contracts\HasLabel;

enum CategoryType: string implements HasLabel
{
    case PROPERTY = 'property';
    case TEMPLATE = 'template';
    case CLIENT_TAG = 'client_tag';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PROPERTY => 'Property',
            self::TEMPLATE => 'Template',
            self::CLIENT_TAG => 'Client Tag',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PROPERTY => 'success',
            self::TEMPLATE => 'primary',
            self::CLIENT_TAG => 'gray',
        };
    }
}
