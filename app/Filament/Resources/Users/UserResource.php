<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-users';

    protected static ?string $navigationLabel = 'Team Users';

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 1;

    /*
     |----------------------------------------------------------------------
     | Global Search Configuration (Team Members Lookup)
     |----------------------------------------------------------------------
     */
    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var User $record */
        $roles = $record->roles->pluck('name')->implode(', ');

        return [
            'Email'  => $record->email,
            'Role'   => ! empty($roles) ? $roles : 'No Role',
            'Status' => $record->is_active ? 'Active' : 'Suspended',
        ];
    }



    public static function getNavigationBadge(): ?string
    {
        $count = User::where('id', '!=', 1)->where('is_active', true)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active agents and brokers (Ghost Admin excluded)';
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    /*
     |----------------------------------------------------------------------
     | Ghost Admin Security Invariant
     |----------------------------------------------------------------------
     | Master User ID 1 (matchmaker@moncefdev.me) must be strictly scoped
     | out of all presentation tables and global search queries for
     | non-root users.
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        $query = parent::getGlobalSearchEloquentQuery()->with(['roles']);

        if (auth()->id() !== 1) {
            $query->where('id', '!=', 1);
        }

        return $query;
    }
}
