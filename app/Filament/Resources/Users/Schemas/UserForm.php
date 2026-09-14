<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->description('Core account identity, authentication credentials, and access status.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->prefixIcon('heroicon-m-user')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Yanis Bouzid')
                            ->validationMessages([
                                'required' => "Please enter the user's full name.",
                                'max' => 'The user name cannot exceed 255 characters.',
                            ]),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->prefixIcon('heroicon-m-envelope')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g. yanis.bouzid@cadastre.test')
                            ->validationMessages([
                                'required' => 'A valid corporate email address is required.',
                                'email' => 'Please enter a valid email address format.',
                                'unique' => 'A user account with this email address already exists.',
                                'max' => 'Email address cannot exceed 255 characters.',
                            ]),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->prefixIcon('heroicon-m-phone')
                            ->prefix('+')
                            ->maxLength(50)
                            ->placeholder('213 550 11 22 33')
                            ->formatStateUsing(fn (?string $state): ?string => $state ? ltrim(trim($state), '+') : null)
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? '+' . ltrim(trim($state), '+') : null)
                            ->validationMessages([
                                'max' => 'Phone number cannot exceed 50 characters.',
                            ]),

                        TextInput::make('password')
                            ->label('Account Password')
                            ->password()
                            ->revealable()
                            ->prefixIcon('heroicon-m-key')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255)
                            ->helperText(fn (string $context): ?string => $context === 'edit' ? 'Leave blank to retain the current password.' : null)
                            ->validationMessages([
                                'required' => 'A secure account password is required for new team members.',
                                'max' => 'Password cannot exceed 255 characters.',
                            ]),

                        Toggle::make('is_active')
                            ->label('Account Active Status')
                            ->default(true)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Role Management')
                    ->description('Assign Spatie Shield roles to control permissions and business gates.')
                    ->schema([
                        CheckboxList::make('roles')
                            ->label('Assigned Roles')
                            ->relationship(
                                name: 'roles',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query->whereNotIn('name', ['super_admin', 'Super Admin'])
                            )
                            ->searchable()
                            ->columns(3)
                            ->bulkToggleable()
                            ->columnSpanFull()
                            ->helperText('Select the roles that dictate this team member\'s authorization scope and resource access.'),
                    ]),
            ]);
    }
}
