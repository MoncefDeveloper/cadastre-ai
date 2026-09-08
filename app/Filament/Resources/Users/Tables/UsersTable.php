<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                if (auth()->id() !== 1) {
                    return $query->where('id', '!=', 1);
                }

                return $query;
            })
            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->disabled(fn(User $record): bool => ! auth()->user()->can('Update:User') || $record->id === auth()->id())
                    ->sortable(),
                TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_login_ip')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])

            ->recordActions([
                EditAction::make()
                    ->outlined()
                    ->iconSize('sm')
                    ->size('sm')
                    ->button(),
                Action::make('force_logout')
                    ->label('Force Logout')
                    ->outlined()
                    ->button()
                    ->iconSize('sm')
                    ->size('sm')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(User $record): bool => auth()->user()->can('force_logout_users') && $record->id !== auth()->id())
                    ->action(function (User $record): void {
                        DB::table('sessions')
                            ->where('user_id', $record->id)
                            ->delete();

                        $record->forceFill([
                            'remember_token' => \Illuminate\Support\Str::random(60),
                        ])->save();

                        Notification::make()
                            ->title('Sessions Terminated')
                            ->body("All active sessions and remember-cookies for {$record->name} have been revoked.")
                            ->success()
                            ->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
