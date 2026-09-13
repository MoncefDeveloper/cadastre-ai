<?php

declare(strict_types=1);

namespace App\Filament\Resources\Properties\Pages;

use App\Filament\Resources\Properties\PropertyResource;
use App\Services\Property\PropertyImageService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->outlined(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['image_uploads'] = $this->getRecord()->images()->orderBy('sort_order')->pluck('image_path')->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['image_uploads']);

        return $data;
    }

    protected function afterSave(): void
    {
        $images = $this->data['image_uploads'] ?? [];

        app(PropertyImageService::class)->syncImages($this->getRecord(), $images);
    }
}
