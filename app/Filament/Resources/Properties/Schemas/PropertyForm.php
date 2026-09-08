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
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(callable $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                            TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true),

                            MarkdownEditor::make('description')
                                ->required()
                                // ->fileAttachmentsDisk()
                                ->columnSpanFull(),
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
                                ->columnSpan(1),

                            TextInput::make('address')
                                ->columnSpan(1),

                            TextInput::make('area_sqm')
                                ->label('Area (m²)')
                                ->numeric()
                                ->required(),

                            TextInput::make('bedrooms')
                                ->numeric()
                                ->default(0),

                            TextInput::make('bathrooms')
                                ->numeric()
                                ->default(0),
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
                                ->required(),

                            Select::make('category_id')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('listing_type')
                                ->options(ListingType::class)
                                ->required(),

                            Select::make('property_type')
                                ->options(PropertyType::class)
                                ->required(),

                            Select::make('status')
                                ->options(PropertyStatus::class)
                                ->default(PropertyStatus::AVAILABLE)
                                ->required()
                                ->disabled(
                                    fn(?Property $record): bool =>
                                    $record !== null &&
                                        in_array($record->status, [PropertyStatus::SOLD, PropertyStatus::RENTED], true) &&
                                        ! auth()->user()->can('reopen_closed_listings')
                                ),

                            TextInput::make('price')
                                ->required()
                                ->numeric()
                                ->prefix('€')
                                ->formatStateUsing(fn($state) => $state ? $state / 100 : null)
                                ->dehydrateStateUsing(fn($state) => (int) ($state * 100))
                                ->disabled(fn(string $context): bool => $context === 'edit' && ! auth()->user()->can('update_property_pricing'))
                                ->dehydrated(fn(string $context): bool => $context === 'create' || auth()->user()->can('update_property_pricing')),

                            TextInput::make('discount_price')
                                ->numeric()
                                ->prefix('€')
                                ->formatStateUsing(fn($state) => $state ? $state / 100 : null)
                                ->dehydrateStateUsing(fn($state) => $state ? (int) ($state * 100) : null)
                                ->disabled(fn(string $context): bool => $context === 'edit' && ! auth()->user()->can('update_property_pricing'))
                                ->dehydrated(fn(string $context): bool => $context === 'create' || auth()->user()->can('update_property_pricing')),
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
                                ->disabled(fn(?Property $record): bool => auth()->id() !== 1 && $record !== null && $record->isBaselineRecord())
                                ->helperText(fn(?Property $record): ?string => auth()->id() !== 1 && $record?->isBaselineRecord() ? '🛡️ Baseline property media gallery is locked from modification in demo mode.' : null),
                        ]),
                ])
                    ->skippable() // Allows agents to jump between tabs easily
                    ->persistStepInQueryString() // Keeps current step on page refresh
                    ->columnSpanFull(), // Ensures the wizard takes the full width of the page
            ]);
    }
}
