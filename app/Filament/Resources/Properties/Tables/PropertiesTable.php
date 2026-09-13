<?php

declare(strict_types=1);

namespace App\Filament\Resources\Properties\Tables;

use App\Enums\Property\ListingType;
use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class PropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn (Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No properties found')
            ->emptyStateDescription('Start by creating a new luxury real estate listing.')
            ->emptyStateIcon('heroicon-o-home-modern')
            ->columns([
                // 1. Property Title (Max 30 Chars + Hover Tooltip + Stacked City Subtitle)
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Property $record): ?string => $record->title)
                    ->description(function (Property $record): ?HtmlString {
                        if (blank($record->city)) {
                            return null;
                        }

                        $city = (string) $record->city;
                        $truncatedCity = str($city)->limit(30)->toString();

                        return new HtmlString('<span title="' . e($city) . '" class="cursor-help text-xs text-gray-500 dark:text-gray-400">' . e($truncatedCity) . '</span>');
                    }),

                // 2. Managing Agent (Centered)
                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable()
                    ->tooltip(fn (Property $record): ?string => $record->agent?->name),

                // 3. Price (Centered)
                TextColumn::make('price')
                    ->label('Price')
                    ->money('EUR', divideBy: 100)
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (Property $record): string => '€' . number_format($record->price / 100, 2)),

                // 4. Listing Type Badge (Centered)
                TextColumn::make('listing_type')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),

                // 5. Deal Status Badge (Centered)
                TextColumn::make('status')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),

                // 6. Featured Status Toggle (Centered)
                ToggleColumn::make('is_featured')
                    ->label('Featured')
                    ->sortable()
                    ->alignCenter()
                    ->disabled(fn (Property $record): bool => ! auth()->user()->can('Update:Property', $record)),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(PropertyStatus::class)
                    ->label('Deal Status'),

                SelectFilter::make('listing_type')
                    ->options(ListingType::class)
                    ->label('Listing Type'),
            ])
            ->recordActions([
                // Standardized Action Button: info color, outlined, sm size
                EditAction::make()
                    ->color('info')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
