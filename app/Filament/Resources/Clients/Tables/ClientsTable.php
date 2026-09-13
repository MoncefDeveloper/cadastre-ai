<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients\Tables;

use App\Enums\ClientStatus;
use App\Models\Client;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn (Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No clients found')
            ->emptyStateDescription('Start by registering a new client investor profile.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->columns([
                // 1. First Name (Max 30 Chars + Hover Tooltip)
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Client $record): ?string => $record->first_name),

                // 2. Last Name (Max 30 Chars + Hover Tooltip)
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Client $record): ?string => $record->last_name),

                // 3. Email (Copyable + Tooltip)
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->limit(30)
                    ->tooltip(fn (Client $record): ?string => $record->email),

                // 4. Phone (Centered & Toggleable)
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->alignCenter()
                    ->placeholder('—')
                    ->toggleable(),

                // 5. Status Badge (Centered)
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),

                // 6. Registration Source (Toggleable, Hidden by Default)
                TextColumn::make('source')
                    ->label('Source')
                    ->toggleable(isToggledHiddenByDefault: true),

                // 7. Date Column Rendered as a Centered Badge with Exact Hover Tooltip
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->badge()
                    ->color('gray')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (Client $record): ?string => $record->created_at?->format('M d, Y - h:i A'))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ClientStatus::class)
                    ->label('Client Status'),
            ])
            ->recordActions([
                // Outlined info button only (Trash button completely removed from table)
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
