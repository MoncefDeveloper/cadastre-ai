<?php

declare(strict_types=1);

namespace App\Filament\Resources\Templates\Tables;

use App\Enums\Thread\ThreadChannel;
use App\Models\Template;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn (Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No templates found')
            ->emptyStateDescription('Start by creating an AI prompt template blueprint.')
            ->emptyStateIcon('heroicon-o-document-duplicate')
            ->columns([
                // 1. Blueprint Name (Max 30 Chars + Hover Tooltip)
                TextColumn::make('name')
                    ->label('Blueprint Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Template $record): ?string => $record->name),

                // 2. Channel Badge (Centered with real words: Email, WhatsApp, Webform)
                TextColumn::make('channel')
                    ->label('Channel')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),

                // 3. Category Scope (Centered with Global fallback & Tooltip)
                TextColumn::make('category.name')
                    ->label('Category Scope')
                    ->alignCenter()
                    ->placeholder('Global')
                    ->tooltip(fn (Template $record): string => $record->category?->name ?? 'Global Scope')
                    ->toggleable(),

                // 4. Active Status Toggle (Centered)
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->alignCenter()
                    ->disabled(fn (): bool => ! auth()->user()->can('toggle_template_status'))
                    ->toggleable(),

                // 5. Creation Timestamp (Centered Gray Badge with Exact Datetime Tooltip)
                TextColumn::make('created_at')
                    ->label('Created')
                    ->badge()
                    ->color('gray')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (Template $record): ?string => $record->created_at?->format('M d, Y - h:i A'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('channel')
                    ->options(ThreadChannel::class)
                    ->label('Delivery Channel'),

                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category Scope'),
            ])
            ->recordActions([
                // Edit Action: Outlined info button
                EditAction::make()
                    ->color('info')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm'),

                // Replicate/Clone Action: Outlined success button
                ReplicateAction::make()
                    ->label('Clone')
                    ->color('success')
                    ->icon('heroicon-o-document-duplicate')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm')
                    ->modalWidth('md')
                    ->visible(fn (): bool => auth()->user()->can('replicate_ai_templates'))
                    ->beforeReplicaSaved(function (Model $replica): void {
                        $replica->name = $replica->name . ' (Copy)';
                    }),

                // Delete Action: Outlined primary button as instructed
                DeleteAction::make()
                    ->color('primary')
                    ->icon('heroicon-o-trash')
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
