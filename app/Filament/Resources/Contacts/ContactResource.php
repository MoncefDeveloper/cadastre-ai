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
use UnitEnum;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Inquiries';

    protected static string|UnitEnum|null $navigationGroup = 'Client CRM';

    protected static ?int $navigationSort = 2;

    /*
     |----------------------------------------------------------------------
     | Global Search Configuration (Inbound Inquiries Lookup)
     |----------------------------------------------------------------------
     */
    protected static ?string $recordTitleAttribute = 'subject';

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Contact $record */
        return $record->subject ?? "Inquiry from {$record->name}";
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone', 'subject'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Contact $record */
        return [
            'From'   => "{$record->name} ({$record->email})",
            'Status' => $record->status->getLabel(),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Contact::where('status', ContactMessageStatus::Unread)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Unread webform messages awaiting response';
    }

    public static function canCreate(): bool
    {
        return false;
    }

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
            ->poll('15s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn(Contact $record): ?string => $record->subject)
                    ->weight(fn(Contact $record): string => $record->status === ContactMessageStatus::Unread ? 'bold' : 'normal'),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

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
                ViewAction::make()
                    ->slideOver()
                    ->color('gray'),
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
