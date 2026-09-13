<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients\Schemas;

use App\Enums\ClientStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Details')
                    ->description('Primary contact identity and communication channels.')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->prefixIcon('heroicon-m-user')
                            ->required()
                            ->maxLength(100)
                            ->validationMessages([
                                'required' => "Please provide the client's first name.",
                                'max' => 'First name cannot exceed 100 characters.',
                            ]),

                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->maxLength(100)
                            ->validationMessages([
                                'max' => 'Last name cannot exceed 100 characters.',
                            ]),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->prefixIcon('heroicon-m-envelope')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'A valid email address is required.',
                                'email' => 'Please enter a valid email format.',
                                'unique' => 'A client with this email address already exists in the CRM.',
                                'max' => 'Email address cannot exceed 255 characters.',
                            ]),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->prefixIcon('heroicon-m-phone')
                            ->maxLength(50)
                            ->validationMessages([
                                'max' => 'Phone number cannot exceed 50 characters.',
                            ]),
                    ])
                    ->columns(2),

                Section::make('CRM Metadata')
                    ->description('Lead origin tracking and investor stage categorization.')
                    ->schema([
                        Select::make('status')
                            ->label('Investor Stage')
                            ->options(ClientStatus::class)
                            ->default(ClientStatus::NEW)
                            ->required()
                            ->native(false)
                            ->validationMessages([
                                'required' => 'Please select an initial CRM status for this client.',
                            ]),

                        TextInput::make('source')
                            ->label('Acquisition Source')
                            ->default('Manual Entry')
                            ->required()
                            ->maxLength(100)
                            ->validationMessages([
                                'required' => 'Please identify the lead acquisition source.',
                                'max' => 'Lead source cannot exceed 100 characters.',
                            ]),
                    ])
                    ->columns(2),
            ]);
    }
}
