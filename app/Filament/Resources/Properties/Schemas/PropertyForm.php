<?php

declare(strict_types=1);

namespace App\Filament\Resources\Properties\Schemas;

use App\Enums\Property\ListingType;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Models\Property;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([

                    // STEP 1: GENERAL INFO
                    Step::make('General')
                        ->description('Basic property details')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->completedIcon(Heroicon::CheckBadge)
                        ->columns(2)
                        ->schema([
                            TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (callable $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                                ->validationMessages([
                                    'required' => 'Please enter a descriptive title for this property listing.',
                                    'max' => 'The property title cannot exceed 255 characters.',
                                ]),

                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->validationMessages([
                                    'required' => 'A unique URL slug is required for web matching.',
                                    'unique' => 'This URL slug is already assigned to another property.',
                                ]),

                            MarkdownEditor::make('description')
                                ->required()
                                ->columnSpanFull()
                                ->validationMessages([
                                    'required' => 'Please provide a detailed architectural description of the property.',
                                ]),
                        ]),

                    // STEP 2: LOCATION & SPECS
                    Step::make('Specifications')
                        ->description('Location and dimensions')
                        ->icon(Heroicon::OutlinedMapPin)
                        ->completedIcon(Heroicon::CheckBadge)
                        ->columns(2)
                        ->schema([
                            TextInput::make('city')
                                ->required()
                                ->maxLength(100)
                                ->columnSpan(1)
                                ->validationMessages([
                                    'required' => 'The property city is required for geographical lead matching.',
                                ]),

                            TextInput::make('address')
                                ->maxLength(255)
                                ->columnSpan(1),

                            TextInput::make('area_sqm')
                                ->label('Area (m²)')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->validationMessages([
                                    'required' => 'Please specify the total area in square meters.',
                                    'numeric' => 'The area must be a valid numerical value.',
                                    'min' => 'The area must be at least 1 square meter.',
                                ]),

                            TextInput::make('bedrooms')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->validationMessages([
                                    'numeric' => 'Bedrooms must be a valid number.',
                                    'min' => 'Bedrooms cannot be negative.',
                                ]),

                            TextInput::make('bathrooms')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->validationMessages([
                                    'numeric' => 'Bathrooms must be a valid number.',
                                    'min' => 'Bathrooms cannot be negative.',
                                ]),
                        ]),

                    // STEP 3: CLASSIFICATION & PRICING
                    Step::make('Classification')
                        ->description('Listing type and financials')
                        ->icon(Heroicon::OutlinedCurrencyDollar)
                        ->completedIcon(Heroicon::CheckBadge)
                        ->columns(2)
                        ->schema([
                            Select::make('agent_id')
                                ->relationship('agent', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->validationMessages([
                                    'required' => 'Please assign a managing real estate agent to this property.',
                                ]),

                            Select::make('category_id')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->validationMessages([
                                    'required' => 'Please select a portfolio category for this listing.',
                                ]),

                            Select::make('listing_type')
                                ->options(ListingType::class)
                                ->required()
                                ->validationMessages([
                                    'required' => 'Please specify whether this listing is for Sale or for Rent.',
                                ]),

                            Select::make('property_type')
                                ->options(PropertyType::class)
                                ->required()
                                ->validationMessages([
                                    'required' => 'Please select a property classification type (e.g. Villa, Penthouse).',
                                ]),

                            Select::make('status')
                                ->options(PropertyStatus::class)
                                ->default(PropertyStatus::AVAILABLE)
                                ->required()
                                ->disabled(
                                    fn (?Property $record): bool =>
                                    $record !== null &&
                                        in_array($record->status, [PropertyStatus::SOLD, PropertyStatus::RENTED], true) &&
                                        ! auth()->user()->can('reopen_closed_listings')
                                )
                                ->validationMessages([
                                    'required' => 'The property transaction status must be set.',
                                ]),

                            TextInput::make('price')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->prefix('€')
                                ->formatStateUsing(fn ($state) => $state ? $state / 100 : null)
                                ->dehydrateStateUsing(fn ($state) => (int) ($state * 100))
                                ->disabled(fn (string $context): bool => $context === 'edit' && ! auth()->user()->can('update_property_pricing'))
                                ->dehydrated(fn (string $context): bool => $context === 'create' || auth()->user()->can('update_property_pricing'))
                                ->validationMessages([
                                    'required' => 'Please enter the listing acquisition or lease price.',
                                    'numeric' => 'Price must be a valid numerical value.',
                                    'min' => 'Price must be greater than zero.',
                                ]),

                            TextInput::make('discount_price')
                                ->numeric()
                                ->minValue(1)
                                ->prefix('€')
                                ->formatStateUsing(fn ($state) => $state ? $state / 100 : null)
                                ->dehydrateStateUsing(fn ($state) => $state ? (int) ($state * 100) : null)
                                ->disabled(fn (string $context): bool => $context === 'edit' && ! auth()->user()->can('update_property_pricing'))
                                ->dehydrated(fn (string $context): bool => $context === 'create' || auth()->user()->can('update_property_pricing'))
                                ->validationMessages([
                                    'numeric' => 'Discount price must be a valid numerical value.',
                                    'min' => 'Discount price must be greater than zero.',
                                ]),
                        ]),

                    // STEP 4: MEDIA & VISIBILITY
                    Step::make('Media')
                        ->description('Photos and visibility')
                        ->icon(Heroicon::OutlinedPhoto)
                        ->completedIcon(Heroicon::CheckBadge)
                        ->schema([
                            Toggle::make('is_featured')
                                ->label('Feature this property on the homepage')
                                ->default(false),

                            FileUpload::make('image_uploads')
                                ->label('Drag & Drop Gallery')
                                ->visibility('public')
                                ->disk('public')
                                ->multiple()
                                ->directory('properties')
                                ->image()
                                ->reorderable()
                                ->appendFiles()
                                ->panelLayout('grid')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(10240)
                                ->columnSpanFull()
                                ->disabled(fn (?Property $record): bool => auth()->id() !== 1 && $record !== null && $record->isBaselineRecord())
                                ->helperText(fn (?Property $record): ?string => auth()->id() !== 1 && $record?->isBaselineRecord() ? '🛡️ Baseline property media gallery is locked from modification in demo mode.' : null),
                        ]),
                ])
                    ->skippable()
                    ->persistStepInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}
