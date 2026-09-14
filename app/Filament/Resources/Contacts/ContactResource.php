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
use Illuminate\Support\HtmlString;
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
                    ->label('Inquiry Status')
                    ->options(ContactMessageStatus::class)
                    ->required()
                    ->native(false)
                    ->validationMessages([
                        'required' => 'Please select an inquiry status.',
                    ]),
            ])
            ->columns(1);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sender Details')
                    ->description('Contact identity, reachability and security IP metadata.')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Sender Name')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('email')
                            ->label('Email Address')
                            ->copyable()
                            ->icon(Heroicon::Envelope),

                        TextEntry::make('phone')
                            ->label('Phone Number')
                            ->copyable()
                            ->icon(Heroicon::Phone)
                            ->placeholder('—'),

                        TextEntry::make('ip_address')
                            ->label('Origin IP Address')
                            ->placeholder('N/A'),
                    ]),

                Section::make('Message Body')
                    ->description('Full raw inquiry transmitted via web portal.')
                    ->schema([
                        TextEntry::make('subject')
                            ->label('Subject')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('message')
                            ->label('Message Content')
                            ->markdown(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn (Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->poll('15s')
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No inquiries found')
            ->emptyStateDescription('Inbound webform inquiries will appear here in real-time.')
            ->emptyStateIcon('heroicon-o-chat-bubble-bottom-center-text')
            ->columns([
                // 1. Sender Name (Max 30 Chars + Hover Tooltip + Stacked Subject)
                TextColumn::make('name')
                    ->label('Sender')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Contact $record): ?string => $record->name)
                    ->description(function (Contact $record): ?HtmlString {
                        if (blank($record->subject)) {
                            return null;
                        }

                        $subject = (string) $record->subject;
                        $truncated = str($subject)->limit(30)->toString();

                        return new HtmlString('<span title="' . e($subject) . '" class="cursor-help text-xs text-gray-500 dark:text-gray-400">' . e($truncated) . '</span>');
                    })
                    ->weight(fn (Contact $record): string => $record->status === ContactMessageStatus::Unread ? 'bold' : 'normal'),

                // 2. Email Address (Copyable + Tooltip + Toggleable)
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->copyable()
                    ->limit(30)
                    ->tooltip(fn (Contact $record): ?string => $record->email)
                    ->icon('heroicon-m-envelope')
                    ->toggleable(),

                // 3. Phone (Centered & Toggleable)
                TextColumn::make('phone')
                    ->label('Phone Number')
                    ->searchable()
                    ->alignCenter()
                    ->placeholder('—')
                    ->toggleable(),

                // 4. Status Dropdown Column (Centered with Spacing Gutter)
                SelectColumn::make('status')
                    ->label('Status')
                    ->options(ContactMessageStatus::class)
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['class' => 'px-6'])
                    ->extraCellAttributes(['class' => 'px-6'])
                    ->disabled(fn (): bool => ! auth()->user()->can('Update:Contact'))
                    ->toggleable(),

                // 5. Received Timestamp (Centered Gray Badge with Exact Datetime Tooltip)
                TextColumn::make('created_at')
                    ->label('Received')
                    ->badge()
                    ->color('gray')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (Contact $record): ?string => $record->created_at?->format('M d, Y - h:i A'))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ContactMessageStatus::class)
                    ->label('Inquiry Status')
                    ->default(ContactMessageStatus::Unread->value),
            ])
            ->recordActions([
                // View Action: Outlined info button triggering the Slide-Over Dossier
                ViewAction::make()
                    ->slideOver()
                    ->color('info')
                    ->icon('heroicon-o-eye')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm'),

                // Delete Action: Outlined primary button as requested to prevent color clash
                DeleteAction::make()
                    ->color('primary')
                    ->icon('heroicon-o-trash')
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

    public static function getPages(): array
    {
        return [
            'index' => ManageContacts::route('/'),
        ];
    }
}
