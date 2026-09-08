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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Coupon Configuration')
                    ->headerActions([
                        // ⚡ Quick Fill Action (100% Non-destructive)
                        Action::make('quickFill')
                            ->label('⚡ Quick Fill')
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
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                            ->dehydrateStateUsing(fn(string $state): string => strtoupper($state))
                            ->suffixAction(
                                Action::make('generate')
                                    ->icon('heroicon-m-sparkles')
                                    ->action(fn(Set $set) => $set('code', strtoupper(Str::random(8))))
                            )
                            ->columnSpanFull(),

                        ToggleButtons::make('type')
                            ->options([
                                'percentage' => 'Percentage (%)',
                                'fixed' => 'Fixed Amount',
                            ])
                            ->default('percentage')
                            ->required()
                            ->inline()
                            ->live() // Triggers reactivity for the Value/Currency fields
                            ->columnSpanFull(),

                        TextInput::make('value')
                            ->required()
                            ->numeric()
                            ->label(fn(Get $get) => $get('type') === 'percentage' ? 'Discount Percentage (%)' : 'Discount Amount')
                            ->maxValue(fn(Get $get) => $get('type') === 'percentage' ? 100 : null)
                            ->formatStateUsing(fn(?int $state, Get $get): ?string => $get('type') === 'fixed' && $state !== null ? (string) round($state / 100, 2) : (string) $state)
                            ->dehydrateStateUsing(fn(?string $state, Get $get): int => $state ? ($get('type') === 'fixed' ? (int) round(((float) $state) * 100) : (int) $state) : 0),

                        Select::make('currency')
                            ->options([
                                'SAR' => 'SAR (Saudi Riyal)',
                                'USD' => 'USD (US Dollar)',
                                'EUR' => 'EUR (Euro)',
                            ])
                            ->default('USD')
                            ->required(fn(Get $get) => $get('type') === 'fixed')
                            ->visible(fn(Get $get) => $get('type') === 'fixed'),
                    ]),

                Section::make('Limits & Validity')
                    ->columns(2)
                    ->components([
                        TextInput::make('limit_uses')
                            ->label('Maximum Uses')
                            ->numeric()
                            ->nullable()
                            ->placeholder('Leave empty for unlimited'),

                        TextInput::make('use_count')
                            ->label('Times Redeemed')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(false),

                        DateTimePicker::make('valid_from')
                            ->nullable()
                            ->native(false),

                        DateTimePicker::make('valid_until')
                            ->nullable()
                            ->native(false),

                        Toggle::make('is_active')
                            ->label('Active Coupon')
                            ->default(true)
                            ->columnSpanFull()
                            ->disabled(fn(): bool => ! auth()->user()->can('toggle_coupon_status')),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->recordTitleAttribute('code')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Coupon code copied')
                    ->copyMessageDuration(1500)
                    ->weight('bold'),

                TextColumn::make('value')
                    ->label('Discount')
                    ->formatStateUsing(function (int $state, Coupon $record): string {
                        if ($record->type === 'percentage') {
                            return "{$state}%";
                        }
                        return Number::currency($state / 100, in: $record->currency ?? 'SAR');
                    })
                    ->badge()
                    ->color('success'),

                TextColumn::make('usage')
                    ->label('Usage')
                    ->state(fn(Coupon $record): string => "{$record->use_count} / " . ($record->limit_uses ?? '∞'))
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->state(function (Coupon $record): string {
                        if (! $record->is_active) return 'Disabled';
                        if (! $record->isValid()) return 'Depleted/Expired';
                        return 'Valid';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Valid' => 'success',
                        'Disabled' => 'gray',
                        'Depleted/Expired' => 'danger',
                        default => 'gray',
                    }),

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->alignCenter()
                    ->disabled(fn(): bool => ! auth()->user()->can('toggle_coupon_status')),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
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
            'index' => ManageCoupons::route('/'),
        ];
    }
}
