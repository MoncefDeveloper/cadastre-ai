<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contacts;

use App\Enums\ContactMessageStatus;
use App\Filament\Resources\Contacts\Pages\ManageContacts;
use App\Models\Contact;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    // protected static ?string $navigationGroup = 'SaaS Admin';

    protected static ?int $navigationSort = 4;

    // Dynamically display the unread badge count in the sidebar!
    public static function getNavigationBadge(): ?string
    {
        $count = Contact::where('status', ContactMessageStatus::Unread)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // Completely disable standard creation
    public static function canCreate(): bool
    {
        return false;
    }

    // The Form is ONLY used when clicking Edit to change the status
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->options(ContactMessageStatus::class)
                    ->required(),
            ])->columns(1);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sender Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('email')
                            ->copyable()
                            ->icon(Heroicon::Envelope),

                        TextEntry::make('phone')
                            ->copyable()
                            ->icon(Heroicon::Phone),

                        TextEntry::make('ip_address')
                            ->label('IP Address'),
                    ]),

                Section::make('Message')
                    ->schema([
                        TextEntry::make('subject')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('message')
                            ->markdown(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->poll('15s') // Auto-refresh the inbox
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn(Contact $record): ?string => $record->subject) // Stack subject under name
                    // Bolds the name if the message is unread
                    ->weight(fn(Contact $record): string => $record->status === ContactMessageStatus::Unread ? 'bold' : 'normal'),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

                // Instant inline editing of the status directly from the table
                SelectColumn::make('status')
                    ->options(ContactMessageStatus::class)
                    ->sortable()
                    ->searchable()
                    ->disabled(fn(): bool => ! auth()->user()->can('Update:Contact')),

                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ContactMessageStatus::class)
                    ->default(ContactMessageStatus::Unread->value),
            ])
            ->recordActions([
                ViewAction::make()->slideOver(), // Opens the beautiful Infolist
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContacts::route('/'),
        ];
    }
}
