<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients;

use App\Enums\ClientStatus;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Clients\Tables\ClientsTable;
use App\Models\Client;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-user-group';

    protected static ?string $navigationLabel = 'Clients';

    protected static string|UnitEnum|null $navigationGroup = 'Client CRM';

    protected static ?int $navigationSort = 1;

    /*
     |----------------------------------------------------------------------
     | Global Search Configuration (Full-Name & Contact Lookup)
     |----------------------------------------------------------------------
     */
    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Client $record */
        return trim("{$record->first_name} {$record->last_name}");
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email', 'phone'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Client $record */
        return [
            'Email'  => $record->email,
            'Phone'  => $record->phone ?? 'N/A',
            'Status' => $record->status->getLabel(),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Client::where('status', ClientStatus::ACTIVE)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active VIP investors and qualified buyers';
    }

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
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
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }
}
