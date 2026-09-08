<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Enums\ClientStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Details')
                    ->schema([
                        TextInput::make('first_name')->required(),
                        TextInput::make('last_name'),
                        TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                        TextInput::make('phone')->tel(),
                    ])->columns(2),

                Section::make('CRM Metadata')
                    ->schema([
                        Select::make('status')
                            ->options(ClientStatus::class)
                            ->default(ClientStatus::NEW)
                            ->required(),
                        TextInput::make('source')
                            ->default('Manual Entry')
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
