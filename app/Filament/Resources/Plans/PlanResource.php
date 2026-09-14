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
                // SECTION 1: Core Financial Identity
                Section::make('Core Details')
                    ->description('Package identification, URL key, and monthly/yearly pricing tiers.')
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
                                $set('description', 'Designed for expanding real estate brokerages requiring multi-seat AI shared inbox triage and advanced FHA compliance auditing.');
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
                            ->label('Plan Name')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn (string $operation, $state, callable $set) =>
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            )
                            ->validationMessages([
                                'required' => 'Please enter a name for this subscription plan.',
                                'max' => 'The plan name cannot exceed 255 characters.',
                            ]),

                        TextInput::make('slug')
                            ->label('Plan Slug Key')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->readOnly()
                            ->dehydrated()
                            ->validationMessages([
                                'required' => 'A unique plan slug identifier is required.',
                                'unique' => 'This plan slug key is already assigned to another package.',
                            ]),

                        Textarea::make('description')
                            ->label('Package Narrative')
                            ->columnSpanFull()
                            ->rows(4)
                            ->nullable()
                            ->maxLength(1000)
                            ->validationMessages([
                                'max' => 'Plan description cannot exceed 1,000 characters.',
                            ]),

                        TextInput::make('price_monthly')
                            ->label('Monthly Price')
                            ->numeric()
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->required()
                            ->formatStateUsing(fn (?int $state): ?string => $state !== null ? (string) round($state / 100, 2) : null)
                            ->dehydrateStateUsing(fn (?string $state): int => $state ? (int) round(((float) $state) * 100) : 0)
                            ->validationMessages([
                                'required' => 'Monthly subscription price is required.',
                                'numeric' => 'Price must be a valid numerical value.',
                                'min' => 'Price cannot be negative.',
                            ]),

                        TextInput::make('price_yearly')
                            ->label('Yearly Price (Discounted)')
                            ->numeric()
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->required()
                            ->formatStateUsing(fn (?int $state): ?string => $state !== null ? (string) round($state / 100, 2) : null)
                            ->dehydrateStateUsing(fn (?string $state): int => $state ? (int) round(((float) $state) * 100) : 0)
                            ->validationMessages([
                                'required' => 'Yearly subscription price is required.',
                                'numeric' => 'Price must be a valid numerical value.',
                                'min' => 'Price cannot be negative.',
                            ]),

                        Select::make('currency')
                            ->label('Billing Currency')
                            ->columnSpanFull()
                            ->options([
                                'SAR' => 'SAR (Saudi Riyal)',
                                'USD' => 'USD (US Dollar)',
                                'EUR' => 'EUR (Euro)',
                            ])
                            ->default('USD')
                            ->required()
                            ->native(false)
                            ->validationMessages([
                                'required' => 'Please designate a billing currency.',
                            ]),
                    ]),

                // SECTION 2: Marketing Highlights, Caps & Visibility
                Section::make('Marketing & Configuration')
                    ->description('Feature bullets, operational system caps, and homepage highlight card.')
                    ->columns(2)
                    ->components([
                        TagsInput::make('features')
                            ->columnSpanFull()
                            ->label('Feature Highlights')
                            ->placeholder('e.g., 5 Agent Seats')
                            ->helperText('Press Enter to add a marketing bullet point. (Stored as flat JSON array)'),

                        KeyValue::make('limits')
                            ->columnSpanFull()
                            ->label('System Operational Limits')
                            ->keyLabel('Metric Code')
                            ->valueLabel('Limit Value')
                            ->addActionLabel('Add Metric Cap')
                            ->helperText('Define operational limits (e.g. Key: "agents", Value: "5").'),

                        TextInput::make('mock_subscriber_count')
                            ->columnSpanFull()
                            ->label('Active Subscriber Count (Telemetry)')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->validationMessages([
                                'required' => 'Subscriber count must be defined.',
                                'numeric' => 'Subscriber count must be a number.',
                                'min' => 'Subscriber count cannot be negative.',
                            ]),

                        Toggle::make('is_active')
                            ->label('Active Plan')
                            ->default(true)
                            ->disabled(fn (): bool => ! auth()->user()->can('toggle_plan_status')),

                        // Featured toggle input with Info color
                        Toggle::make('is_featured')
                            ->label('Featured (Highlight Card)')
                            ->onColor('info')
                            ->default(false)
                            ->disabled(fn (): bool => ! auth()->user()->can('feature_saas_plans')),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn (Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->recordTitleAttribute('name')
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->emptyStateHeading('No subscription plans found')
            ->emptyStateDescription('Create pricing packages to monetize your real estate CRM.')
            ->emptyStateIcon('heroicon-o-credit-card')
            ->columns([
                // 1. Plan Name (Max 30 Chars + Hover Tooltip)
                TextColumn::make('name')
                    ->label('Plan Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Plan $record): ?string => $record->name)
                    ->weight('bold'),

                // 2. Slug Key (Centered Gray Badge)
                TextColumn::make('slug')
                    ->label('Slug Key')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                // 3. Monthly Price (Centered + Tooltip)
                TextColumn::make('price_monthly')
                    ->label('Monthly')
                    ->formatStateUsing(fn (int $state, Plan $record): string => Number::currency($state / 100, in: $record->currency))
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (Plan $record): string => Number::currency($record->price_monthly / 100, in: $record->currency)),

                // 4. Yearly Price (Centered + Tooltip + Toggleable)
                TextColumn::make('price_yearly')
                    ->label('Yearly')
                    ->formatStateUsing(fn (int $state, Plan $record): string => Number::currency($state / 100, in: $record->currency))
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (Plan $record): string => Number::currency($record->price_yearly / 100, in: $record->currency))
                    ->toggleable(),

                // 5. Subscribers Badge (Centered + Toggleable)
                TextColumn::make('mock_subscriber_count')
                    ->label('Subscribers')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                // 6. Active Toggle (Centered + Toggleable)
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->alignCenter()
                    ->disabled(fn (): bool => ! auth()->user()->can('toggle_plan_status'))
                    ->toggleable(),

                // 7. Featured Toggle (Centered + Info Color + Toggleable)
                ToggleColumn::make('is_featured')
                    ->label('Featured')
                    ->onColor('info')
                    ->sortable()
                    ->alignCenter()
                    ->disabled(fn (): bool => ! auth()->user()->can('feature_saas_plans'))
                    ->toggleable(),

                // 8. Creation Timestamp (Centered Gray Badge with Exact Datetime Tooltip)
                TextColumn::make('created_at')
                    ->label('Created')
                    ->badge()
                    ->color('gray')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (Plan $record): ?string => $record->created_at?->format('M d, Y - h:i A'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status'),

                TernaryFilter::make('is_featured')
                    ->label('Featured Status'),
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
            'index' => ManagePlans::route('/'),
        ];
    }
}
