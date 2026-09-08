<?php

namespace App\Filament\Resources\Templates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('channel')->badge(),
                TextColumn::make('category.name')->label('Category Scope')->default('Global'),
                ToggleColumn::make('is_active')
                    ->sortable()
                    ->disabled(fn(): bool => ! auth()->user()->can('toggle_template_status')),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->outlined()
                    ->button()
                    ->iconSize('sm')
                    ->size('sm'),
                DeleteAction::make()
                    ->outlined()
                    ->button()
                    ->iconSize('sm')
                    ->size('sm'),
                ReplicateAction::make()
                    ->label('Clone')
                    ->outlined()
                    ->button()
                    ->iconSize('sm')
                    ->size('sm')
                    ->color('info')
                    // Hide from users lacking standard clone permissions
                    ->visible(fn(): bool => auth()->user()->can('replicate_ai_templates'))
                    // Clean UX: Append " (Copy)" to the cloned name before saving to DB
                    ->beforeReplicaSaved(function (Model $replica): void {
                        $replica->name = $replica->name . ' (Copy)';
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
