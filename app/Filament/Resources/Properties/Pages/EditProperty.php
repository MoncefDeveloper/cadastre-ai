<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Filament\Resources\Properties\PropertyResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Services\Property\PropertyImageService;


class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pre-fill the FileUpload component with existing images ordered correctly
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

        // Resolve the service from the container and execute
        app(PropertyImageService::class)->syncImages($this->getRecord(), $images);
    }
}
