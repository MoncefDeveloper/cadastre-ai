<?php

declare(strict_types=1);

namespace App\Filament\Resources\Plans;

use App\Filament\Resources\Plans\Pages\ManagePlans;
use App\Models\Plan;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-credit-card';

    protected static ?string $navigationLabel = 'Plans';

    protected static string|UnitEnum|null $navigationGroup = 'Commercial';

    protected static ?int $navigationSort = 1;

    /*
     |----------------------------------------------------------------------
     | Global Search Configuration (Subscription Packages Lookup)
     |----------------------------------------------------------------------
     */
    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Plan $record */
        return [
            'Monthly'  => Number::currency($record->price_monthly / 100, in: $record->currency ?? 'USD'),
            'Status'   => $record->is_active ? 'Active' : 'Disabled',
            'Featured' => $record->is_featured ? 'Yes' : 'No',
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Plan::where('is_active', true)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Published agency subscription packages';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Stacked Section 1
                Section::make('Core Details')
                    ->headerActions([
                        Action::make('quickFill')
                            ->label('Quick Fill')
                            ->icon('heroicon-m-sparkles')
                            ->outlined()
                            ->color('warning')
                            ->action(function (Set $set): void {
                                $rand = rand(100, 999);
                                $name = "Growth Agency Tier #{$rand}";

                                $set('name', $name);
                                $set('slug', Str::slug($name));
                                $set('description', "Designed for expanding real estate brokerages requiring multi-seat AI shared inbox triage and advanced FHA compliance auditing.");
                                $set('price_monthly', '249');
                                $set('price_yearly', '2490');
                                $set('currency', 'USD');
                                $set('features', [
                                    'Unlimited AI Copilot Ingestion',
                                    '100 Active Property Listings',
                                    '5 Agent Seats Included',
                                    'Fair Housing FHA Auditing',
                                    'Dedicated Postmark Delivery Server',
                                ]);
                                $set('limits', [
                                    'agents' => '5',
                                    'listings' => '100',
                                    'storage_gb' => '10',
                                ]);
                                $set('mock_subscriber_count', 145);
                                $set('is_active', true);
                                $set('is_featured', false);
                            }),
                    ])
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn(string $operation, $state, callable $set) =>
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->readOnly()
                            ->dehydrated(),

                        Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(7)
                            ->nullable(),

                        TextInput::make('price_monthly')
                            ->label('Monthly Price')
                            ->numeric()
                            ->inputMode('decimal')
                            ->required()
                            ->formatStateUsing(fn(?int $state): ?string => $state !== null ? (string) round($state / 100, 2) : null)
                            ->dehydrateStateUsing(fn(?string $state): int => $state ? (int) round(((float) $state) * 100) : 0),

                        TextInput::make('price_yearly')
                            ->label('Yearly Price')
                            ->numeric()
                            ->inputMode('decimal')
                            ->required()
                            ->formatStateUsing(fn(?int $state): ?string => $state !== null ? (string) round($state / 100, 2) : null)
                            ->dehydrateStateUsing(fn(?string $state): int => $state ? (int) round(((float) $state) * 100) : 0),

                        Select::make('currency')
                            ->columnSpanFull()
                            ->options([
                                'SAR' => 'SAR (Saudi Riyal)',
                                'USD' => 'USD (US Dollar)',
                                'EUR' => 'EUR (Euro)',
                            ])
                            ->default('USD')
                            ->required(),
                    ]),

                // Stacked Section 2
                Section::make('Marketing & Configuration')
                    ->columns(2)
                    ->components([
                        TagsInput::make('features')
                            ->columnSpanFull()
                            ->label('Feature Highlights')
                            ->placeholder('e.g., 5 Agent Seats')
                            ->helperText('Press Enter to add a feature. (Stored as a flat JSON array)'),

                        KeyValue::make('limits')
                            ->columnSpanFull()
                            ->label('System Limits')
                            ->keyLabel('Metric Code')
                            ->valueLabel('Limit Value')
                            ->addActionLabel('Add Limit')
                            ->helperText('e.g., Key: "agents", Value: "5"'),

                        TextInput::make('mock_subscriber_count')
                            ->columnSpanFull()
                            ->label('Subscribers (Mock)')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active Plan')
                            ->default(true)
                            ->disabled(fn(): bool => ! auth()->user()->can('toggle_plan_status')),

                        Toggle::make('is_featured')
                            ->label('Featured (Highlight Card)')
                            ->default(false)
                            ->disabled(fn(): bool => ! auth()->user()->can('feature_saas_plans')),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->recordTitleAttribute('name')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->label('Plan')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Plan $record): string => $record->slug)
                    ->weight('bold'),

                TextColumn::make('price_monthly')
                    ->label('Monthly')
                    ->formatStateUsing(fn(int $state, Plan $record): string => Number::currency($state / 100, in: $record->currency))
                    ->sortable(),

                TextColumn::make('price_yearly')
                    ->label('Yearly')
                    ->formatStateUsing(fn(int $state, Plan $record): string => Number::currency($state / 100, in: $record->currency))
                    ->sortable(),

                TextColumn::make('mock_subscriber_count')
                    ->label('Subscribers')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->alignCenter()
                    ->disabled(fn(): bool => ! auth()->user()->can('toggle_plan_status')),

                ToggleColumn::make('is_featured')
                    ->label('Featured')
                    ->sortable()
                    ->alignCenter()
                    ->disabled(fn(): bool => ! auth()->user()->can('feature_saas_plans')),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->recordActions([
                EditAction::make()
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
            'index' => ManagePlans::route('/'),
        ];
    }
}
