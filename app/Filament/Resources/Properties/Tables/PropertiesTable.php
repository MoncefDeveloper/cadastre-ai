<?php

declare(strict_types=1);

namespace App\Filament\Resources\Properties\Tables;

use App\Models\Property;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->city),

                TextColumn::make('agent.name')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('price')
                    ->money('EUR', divideBy: 100) // Filament 3/5 natively handles division for money!
                    ->sortable(),

                TextColumn::make('listing_type')->badge(),
                TextColumn::make('status')->badge(),

                ToggleColumn::make('is_featured')
                    ->sortable()
                    ->alignCenter()
                    ->disabled(fn(Property $record): bool => ! auth()->user()->can('Update:Property', $record)),

            ])
            ->filters([
                // We will add robust filters later based on your UI needs
            ])
            ->recordActions([
                EditAction::make()
                    ->outlined()
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
