<?php

declare(strict_types=1);

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages\ManageCoupons;
use App\Models\Coupon;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use UnitEnum;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-ticket';

    protected static ?string $navigationLabel = 'Coupons';

    protected static string|UnitEnum|null $navigationGroup = 'Commercial';

    protected static ?int $navigationSort = 2;

    /*
     |----------------------------------------------------------------------
     | Global Search Configuration (Voucher Codes Lookup)
     |----------------------------------------------------------------------
     */
    protected static ?string $recordTitleAttribute = 'code';

    public static function getGloballySearchableAttributes(): array
    {
        return ['code'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Coupon $record */
        $discount = $record->type === 'percentage'
            ? "{$record->value}%"
            : Number::currency($record->value / 100, in: $record->currency ?? 'USD');

        return [
            'Discount' => $discount,
            'Redeemed' => "{$record->use_count} / " . ($record->limit_uses ?? '∞'),
            'Status'   => $record->isValid() ? 'Valid' : 'Expired/Inactive',
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Coupon::where('is_active', true)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active discount vouchers and checkout promos';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: Core Voucher Parameters
                Section::make('Coupon Configuration')
                    ->description('Voucher code definition, discount calculation rule, and currency.')
                    ->headerActions([
                        Action::make('quickFill')
                            ->label('Quick Fill') // Removed lightning emoji
                            ->icon('heroicon-m-sparkles')
                            ->outlined()
                            ->color('warning')
                            ->action(function (Set $set): void {
                                $rand = rand(10, 99);

                                $set('code', "SUMMER{$rand}");
                                $set('type', 'percentage');
                                $set('value', 25);
                                $set('currency', 'USD');
                                $set('limit_uses', 150);
                                $set('use_count', 0);
                                $set('valid_from', now()->toDateTimeString());
                                $set('valid_until', now()->addMonths(6)->toDateTimeString());
                                $set('is_active', true);
                            }),
                    ])
                    ->columns(2)
                    ->components([
                        TextInput::make('code')
                            ->label('Coupon Code')
                            ->prefixIcon('heroicon-m-ticket')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                            ->dehydrateStateUsing(fn (string $state): string => strtoupper($state))
                            ->suffixAction(
                                Action::make('generate')
                                    ->icon('heroicon-m-sparkles')
                                    ->tooltip('Generate random code')
                                    ->action(fn (Set $set) => $set('code', strtoupper(Str::random(8))))
                            )
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Please enter a unique coupon code.',
                                'unique' => 'This coupon code already exists in the system.',
                                'max' => 'Coupon code cannot exceed 255 characters.',
                            ]),

                        ToggleButtons::make('type')
                            ->label('Discount Type')
                            ->options([
                                'percentage' => 'Percentage (%)',
                                'fixed' => 'Fixed Amount',
                            ])
                            ->default('percentage')
                            ->required()
                            ->inline()
                            ->live()
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Please designate the discount calculation type.',
                            ]),

                        TextInput::make('value')
                            ->label(fn (Get $get): string => $get('type') === 'percentage' ? 'Discount Percentage (%)' : 'Discount Amount')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(fn (Get $get): ?int => $get('type') === 'percentage' ? 100 : null)
                            ->formatStateUsing(fn (?int $state, Get $get): ?string => $get('type') === 'fixed' && $state !== null ? (string) round($state / 100, 2) : (string) $state)
                            ->dehydrateStateUsing(fn (?string $state, Get $get): int => $state ? ($get('type') === 'fixed' ? (int) round(((float) $state) * 100) : (int) $state) : 0)
                            ->validationMessages([
                                'required' => 'Please enter the discount value.',
                                'numeric' => 'Discount value must be a valid number.',
                                'min' => 'Discount value must be greater than zero.',
                                'max' => 'Percentage discounts cannot exceed 100%.',
                            ]),

                        Select::make('currency')
                            ->label('Currency')
                            ->options([
                                'SAR' => 'SAR (Saudi Riyal)',
                                'USD' => 'USD (US Dollar)',
                                'EUR' => 'EUR (Euro)',
                            ])
                            ->default('USD')
                            ->native(false)
                            ->required(fn (Get $get): bool => $get('type') === 'fixed')
                            ->visible(fn (Get $get): bool => $get('type') === 'fixed')
                            ->validationMessages([
                                'required' => 'Please select a currency for fixed discounts.',
                            ]),
                    ]),

                // SECTION 2: Boundaries & Redemption Ceilings
                Section::make('Limits & Validity')
                    ->description('Redemption volume ceilings, temporal active windows, and master toggle.')
                    ->columns(2)
                    ->components([
                        TextInput::make('limit_uses')
                            ->label('Maximum Uses')
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->placeholder('Leave empty for unlimited')
                            ->validationMessages([
                                'numeric' => 'Usage limit must be a valid number.',
                                'min' => 'Usage limit must be at least 1.',
                            ]),

                        TextInput::make('use_count')
                            ->label('Times Redeemed')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(false),

                        DateTimePicker::make('valid_from')
                            ->label('Valid From')
                            ->nullable()
                            ->native(false),

                        DateTimePicker::make('valid_until')
                            ->label('Valid Until')
                            ->nullable()
                            ->native(false),

                        Toggle::make('is_active')
                            ->label('Active Coupon')
                            ->default(true)
                            ->columnSpanFull()
                            ->disabled(fn (): bool => ! auth()->user()->can('toggle_coupon_status')),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn (Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->recordTitleAttribute('code')
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No discount coupons found')
            ->emptyStateDescription('Generate promotional vouchers and checkout discount codes.')
            ->emptyStateIcon('heroicon-o-ticket')
            ->columns([
                // 1. Coupon Code (No Badge, Centered, Monospace Bold + Copyable)
                TextColumn::make('code')
                    ->label('Coupon Code')
                    ->searchable()
                    ->sortable()
                    ->alignCenter()
                    ->copyable()
                    ->copyMessage('Coupon code copied to clipboard')
                    ->copyMessageDuration(1500)
                    ->weight('bold')
                    ->fontFamily('mono'),

                // 2. Discount Value (No Badge, Centered)
                TextColumn::make('value')
                    ->label('Discount')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(function (int $state, Coupon $record): string {
                        if ($record->type === 'percentage') {
                            return "{$state}%";
                        }
                        return Number::currency($state / 100, in: $record->currency ?? 'SAR');
                    })
                    ->weight('medium'),

                // 3. Usage Meter (Centered + Exact Tooltip + Toggleable)
                TextColumn::make('usage')
                    ->label('Redeemed')
                    ->state(fn (Coupon $record): string => "{$record->use_count} / " . ($record->limit_uses ?? '∞'))
                    ->alignCenter()
                    ->tooltip(fn (Coupon $record): string => "{$record->use_count} redemptions out of " . ($record->limit_uses ? "{$record->limit_uses} maximum allowable" : 'unlimited allowable'))
                    ->toggleable(),

                // 4. Status Badge (Centered + Toggleable)
                TextColumn::make('status')
                    ->label('Status')
                    ->state(function (Coupon $record): string {
                        if (! $record->is_active) {
                            return 'Disabled';
                        }
                        if (! $record->isValid()) {
                            return 'Depleted/Expired';
                        }
                        return 'Valid';
                    })
                    ->badge()
                    ->alignCenter()
                    ->color(fn (string $state): string => match ($state) {
                        'Valid' => 'success',
                        'Disabled' => 'gray',
                        'Depleted/Expired' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),

                // 5. Active Toggle (Centered + Toggleable)
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->alignCenter()
                    ->disabled(fn (): bool => ! auth()->user()->can('toggle_coupon_status'))
                    ->toggleable(),

                // 6. Creation Timestamp (Centered Gray Badge with Exact Datetime Tooltip)
                TextColumn::make('created_at')
                    ->label('Created')
                    ->badge()
                    ->color('gray')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (Coupon $record): ?string => $record->created_at?->format('M d, Y - h:i A'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->recordActions([
                // Edit Action: Outlined info button with slide-over
                EditAction::make()
                    ->slideOver()
                    ->color('info')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm'),

                // Delete Action: Outlined primary button with trash icon
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
            'index' => ManageCoupons::route('/'),
        ];
    }
}
