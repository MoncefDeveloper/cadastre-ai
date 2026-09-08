<?php

declare(strict_types=1);

namespace App\Filament\Resources\Properties\Pages;

use App\Enums\Property\ListingType;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Filament\Resources\Properties\PropertyResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * ⚡ Top Header Action: 1-Click Autofill for Evaluators and Testing
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('autofill')
                ->label('Quick Fill')
                ->icon('heroicon-m-sparkles')
                ->color('warning')
                ->outlined()
                ->action(function (): void {
                    $rand = rand(100, 999);

                    $this->form->fill([
                        // Step 1: General Info
                        'title' => "Contemporary Sea-View Villa #{$rand}",
                        'slug' => "contemporary-sea-view-villa-{$rand}",
                        'description' => "### Architectural Masterpiece\n\nStunning contemporary residence featuring panoramic Mediterranean views, infinity pool, Italian marble flooring, and integrated smart-home automation.\n\n* 5 Ensuite master bedrooms\n* Landscaped private gardens & solarium\n* Underground 3-car garage with biometric security",

                        // Step 2: Location & Specs
                        'city' => 'Algiers',
                        'address' => 'Chemin des Crêtes, Hydra',
                        'area_sqm' => 480,
                        'bedrooms' => 5,
                        'bathrooms' => 5,

                        // Step 3: Classification & Financials
                        'agent_id' => 4, // Agent Demo (User ID 4)
                        'category_id' => 2, // Luxury Villas & Penthouses
                        'listing_type' => ListingType::SALE,
                        'property_type' => PropertyType::VILLA,
                        'status' => PropertyStatus::AVAILABLE,
                        'price' => 2_500_000 * 100, // €2,500,000 in cents
                        'discount_price' => 2_350_000 * 100,

                        // Step 4: Media & Visibility
                        'is_featured' => true,
                        'image_uploads' => [
                            'properties/home_placeholder.png',
                            'properties/home_placeholder_2.png',
                        ],
                    ]);

                    Notification::make()
                        ->title('Sample Property Data Filled!')
                        ->body('All 4 wizard steps populated with realistic demo data.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove the virtual field so it doesn't cause a SQL column error
        unset($data['image_uploads']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Access the raw Livewire data array
        $images = $this->data['image_uploads'] ?? [];

        // Use array_values to guarantee sequential keys (0, 1, 2) for sort_order
        foreach (array_values($images) as $index => $path) {
            $this->getRecord()->images()->create([
                'image_path' => $path,
                'sort_order' => $index,
            ]);
        }
    }
}
