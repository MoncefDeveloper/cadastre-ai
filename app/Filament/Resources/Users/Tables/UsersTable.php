<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                // Strict Ghost Admin Invariant: Master User ID 1 is never visible to non-root users
                if (auth()->id() !== 1) {
                    return $query->where('id', '!=', 1);
                }

                return $query;
            })
            ->checkIfRecordIsSelectableUsing(fn (Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No team users found')
            ->emptyStateDescription('Add agents and broker managers to your organization.')
            ->emptyStateIcon('heroicon-o-users')
            ->columns([
                // 1. Team Member Name (Max 30 Chars + Hover Tooltip)
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (User $record): ?string => $record->name)
                    ->weight('bold'),

                // 2. Email Address (Copyable + Tooltip)
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->copyable()
                    ->limit(30)
                    ->tooltip(fn (User $record): ?string => $record->email),

                // 3. Spatie Role Badge (Centered + Semantic Color Mapping)
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->alignCenter()
                    ->placeholder('No Role Assigned')
                    ->color('info')
                    ->toggleable(),

                // 4. Phone (Centered with Placeholder)
                TextColumn::make('phone')
                    ->label('Phone Number')
                    ->searchable()
                    ->alignCenter()
                    ->placeholder('—')
                    ->toggleable(),

                // 5. Active Status Toggle (Centered)
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->alignCenter()
                    ->sortable()
                    ->disabled(fn (User $record): bool => ! auth()->user()->can('Update:User') || $record->id === auth()->id())
                    ->toggleable(),

                // 6. Email Verified At (Centered with Placeholder & Exact Hover Tooltip)
                TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime('M d, Y')
                    ->placeholder('Unverified')
                    ->alignCenter()
                    ->sortable()
                    ->tooltip(fn (User $record): string => $record->email_verified_at ? $record->email_verified_at->format('M d, Y - h:i A') : 'Email address not yet verified')
                    ->toggleable(isToggledHiddenByDefault: true),

                // 7. Last Login Timestamp (Centered Gray Badge with Placeholder & Tooltip)
                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->badge()
                    ->color('gray')
                    ->dateTime('M d, Y')
                    ->placeholder('Never logged in')
                    ->alignCenter()
                    ->sortable()
                    ->tooltip(fn (User $record): string => $record->last_login_at ? $record->last_login_at->format('M d, Y - h:i A') : 'User has never logged in')
                    ->toggleable(),

                // 8. Last Login IP (Centered Monospace with Placeholder)
                TextColumn::make('last_login_ip')
                    ->label('Login IP')
                    ->searchable()
                    ->alignCenter()
                    ->fontFamily('mono')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                // 9. Creation Timestamp (Centered Gray Badge with Datetime Tooltip)
                TextColumn::make('created_at')
                    ->label('Created')
                    ->badge()
                    ->color('gray')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (User $record): ?string => $record->created_at?->format('M d, Y - h:i A'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // Edit Action: Outlined info button
                EditAction::make()
                    ->color('info')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm'),

                // Force Logout Action: Outlined Primary Carmine button with confirmation dialog
                Action::make('force_logout')
                    ->label('Force Logout')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Terminate User Session?')
                    ->modalDescription('This will immediately destroy all active sessions and remember-tokens for this user, forcing them to re-authenticate.')
                    ->modalSubmitActionLabel('Yes, Force Logout')
                    ->visible(fn (User $record): bool => auth()->user()->can('force_logout_users') && $record->id !== auth()->id())
                    ->action(function (User $record): void {
                        DB::table('sessions')
                            ->where('user_id', $record->id)
                            ->delete();

                        $record->forceFill([
                            'remember_token' => Str::random(60),
                        ])->save();

                        Notification::make()
                            ->title('Sessions Terminated')
                            ->body("All active sessions and remember-cookies for {$record->name} have been revoked.")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
