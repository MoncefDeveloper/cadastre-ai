<?php

declare(strict_types=1);

namespace App\Filament\Resources\AiModifiers\Pages;

use App\Filament\Resources\AiModifiers\AiModifierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAiModifiers extends ManageRecords
{
    protected static string $resource = AiModifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('md'),
        ];
    }
}
