<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Pages\ViewRole;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use BezhanSalleh\PluginEssentials\Concerns\Resource as Essentials;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Override;
use UnitEnum;

class RoleResource extends Resource
{
    use Essentials\BelongsToParent;
    use Essentials\BelongsToTenant;
    use Essentials\HasGlobalSearch;
    use Essentials\HasLabels;
    use Essentials\HasNavigation;
    use HasShieldFormComponents;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'heroicon-s-shield-check';

    protected static ?string $navigationLabel = 'Roles & Permissions';

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Administration';
    }

    public static function getNavigationLabel(): string
    {
        return 'Roles & Permissions';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-shield-check';
    }

    public static function getActiveNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-s-shield-check';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Utils::getRoleModel()::count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Configured Shield security roles and permission gates';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'guard_name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Guard'       => $record->guard_name,
            'Permissions' => $record->permissions()->count() . ' Granted',
        ];
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->unique(
                                        ignoreRecord: true,
                                        /** @phpstan-ignore-next-line */
                                        modifyRuleUsing: fn (Unique $rule): Unique => Utils::isTenancyEnabled() ? $rule->where(Utils::getTenantModelForeignKey(), Filament::getTenant()?->id) : $rule
                                    )
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->nullable()
                                    ->maxLength(255),

                                Select::make(config('permission.column_names.team_foreign_key'))
                                    ->label(__('filament-shield::filament-shield.field.team'))
                                    ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                                    /** @phpstan-ignore-next-line */
                                    ->default(Filament::getTenant()?->id)
                                    ->options(fn (): array => in_array(Utils::getTenantModel(), [null, '', '0'], true) ? [] : Utils::getTenantModel()::pluck('name', 'id')->toArray())
                                    ->visible(fn (): bool => static::shield()->isCentralApp() && Utils::isTenancyEnabled())
                                    ->dehydrated(fn (): bool => static::shield()->isCentralApp() && Utils::isTenancyEnabled()),
                                static::getSelectAllFormComponent(),

                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                static::getShieldFormComponents(),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No roles defined')
            ->emptyStateDescription('Configure Spatie Shield security roles and permission gates.')
            ->emptyStateIcon('heroicon-o-shield-check')
            ->columns([
                // 1. Role Title (Max 30 Chars + Headline Capitalization + Tooltip)
                TextColumn::make('name')
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->formatStateUsing(fn (string $state): string => Str::headline($state))
                    ->tooltip(fn (Model $record): string => Str::headline($record->name)),

                // 2. Guard Name (Centered Badge)
                TextColumn::make('guard_name')
                    ->label(__('filament-shield::filament-shield.column.guard_name'))
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->toggleable(),

                // 3. Team / Scope (Centered Badge)
                TextColumn::make('team.name')
                    ->label(__('filament-shield::filament-shield.column.team'))
                    ->default('Global')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (mixed $state): string => str((string) $state)->contains('Global') ? 'gray' : 'primary')
                    ->searchable()
                    ->visible(fn (): bool => static::shield()->isCentralApp() && Utils::isTenancyEnabled())
                    ->toggleable(),

                // 4. Granted Permissions Count (Centered Primary Badge)
                TextColumn::make('permissions_count')
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->formatStateUsing(fn (int $state): string => "{$state} Granted")
                    ->toggleable(),

                // 5. Updated At (Centered Gray Badge with Datetime Tooltip)
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->badge()
                    ->color('gray')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (Model $record): ?string => $record->updated_at?->format('M d, Y - h:i A'))
                    ->toggleable(),

                // 6. Created At (Centered Gray Badge with Datetime Tooltip, Hidden by Default)
                TextColumn::make('created_at')
                    ->label('Created')
                    ->badge()
                    ->color('gray')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (Model $record): ?string => $record->created_at?->format('M d, Y - h:i A'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // Edit Action: Outlined info button
                EditAction::make()
                    ->color('info')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm'),

                // Delete Action: Outlined primary button strictly hidden on super_admin
                DeleteAction::make()
                    ->color('primary')
                    ->icon('heroicon-o-trash')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm')
                    ->hidden(fn (Model $record): bool => in_array($record->name, ['super_admin', 'super-admin', 'Super Admin'], true)),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view'   => ViewRole::route('/{record}'),
            'edit'   => EditRole::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getModel(): string
    {
        return Utils::getRoleModel();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return Utils::getResourceSlug();
    }

    public static function getCluster(): ?string
    {
        return Utils::getResourceCluster();
    }

    public static function getEssentialsPlugin(): ?FilamentShieldPlugin
    {
        return FilamentShieldPlugin::get();
    }

    public static function getShieldFormComponents(): \Filament\Schemas\Components\Component
    {
        $tabs = [
            static::getTabFormComponentForResources(),
            static::getTabFormComponentForPage(),
        ];

        $tabs[] = \Filament\Schemas\Components\Tabs\Tab::make('Custom Permissions')
            ->badge(count(config('filament-shield.custom_permissions', [])))
            ->schema([
                Section::make('Inbox AI')
                    ->description('App\Filament\Pages\Inbox')
                    ->collapsible()
                    ->compact()
                    ->schema([
                        CheckboxList::make('inbox_ai')
                            ->hiddenLabel()
                            ->options([
                                'use_advanced_ai_modifiers'       => 'Use Advanced AI Modifiers',
                                'apply_legally_binding_templates' => 'Apply Legally Binding Templates',
                                'bypass_compliance_gate'          => 'Bypass Compliance Gate',
                            ])
                            ->formatStateUsing(function (?array $state, ?Model $record): array {
                                if (! $record) {
                                    return [];
                                }
                                return $record->permissions()
                                    ->whereIn('name', [
                                        'use_advanced_ai_modifiers',
                                        'apply_legally_binding_templates',
                                        'bypass_compliance_gate',
                                    ])
                                    ->pluck('name')
                                    ->toArray();
                            })
                            ->columns([
                                'default' => 1,
                                'sm'      => 2,
                            ]),
                    ]),
                Section::make('Property Inventory')
                    ->description('App\Filament\Resources\Properties\PropertyResource')
                    ->collapsible()
                    ->compact()
                    ->schema([
                        CheckboxList::make('property_inventory_permissions')
                            ->hiddenLabel()
                            ->options([
                                'update_property_pricing' => 'Update Property Pricing',
                                'reopen_closed_listings'  => 'Reopen Closed Listings',
                            ])
                            ->formatStateUsing(function (?array $state, ?Model $record): array {
                                if (! $record) {
                                    return [];
                                }
                                return $record->permissions()
                                    ->whereIn('name', [
                                        'update_property_pricing',
                                        'reopen_closed_listings',
                                    ])
                                    ->pluck('name')
                                    ->toArray();
                            })
                            ->columns([
                                'default' => 1,
                                'sm'      => 2,
                            ]),
                    ]),
                Section::make('User Security')
                    ->description('App\Filament\Resources\Users\UserResource')
                    ->collapsible()
                    ->compact()
                    ->schema([
                        CheckboxList::make('user_security_permissions')
                            ->hiddenLabel()
                            ->options([
                                'force_logout_users' => 'Force Logout Users',
                            ])
                            ->formatStateUsing(function (?array $state, ?Model $record): array {
                                if (! $record) {
                                    return [];
                                }
                                return $record->permissions()
                                    ->whereIn('name', [
                                        'force_logout_users',
                                    ])
                                    ->pluck('name')
                                    ->toArray();
                            })
                            ->columns([
                                'default' => 1,
                                'sm'      => 2,
                            ]),
                    ]),
                Section::make('AI Templates')
                    ->description('App\Filament\Resources\Templates\TemplateResource')
                    ->collapsible()
                    ->compact()
                    ->schema([
                        CheckboxList::make('ai_templates_permissions')
                            ->hiddenLabel()
                            ->options([
                                'replicate_ai_templates' => 'Replicate AI Templates',
                                'toggle_template_status' => 'Toggle Template Status',
                            ])
                            ->formatStateUsing(function (?array $state, ?Model $record): array {
                                if (! $record) {
                                    return [];
                                }
                                return $record->permissions()
                                    ->whereIn('name', [
                                        'replicate_ai_templates',
                                        'toggle_template_status',
                                    ])
                                    ->pluck('name')
                                    ->toArray();
                            })
                            ->columns([
                                'default' => 1,
                                'sm'      => 2,
                            ]),
                    ]),
                Section::make('AI Modifiers')
                    ->description('App\Filament\Resources\AiModifiers\AiModifierResource')
                    ->collapsible()
                    ->compact()
                    ->schema([
                        CheckboxList::make('ai_modifiers_permissions')
                            ->hiddenLabel()
                            ->options([
                                'manage_global_ai_modifiers' => 'Manage Global AI Modifiers',
                                'toggle_modifier_status'     => 'Toggle Modifier Status',
                            ])
                            ->formatStateUsing(function (?array $state, ?Model $record): array {
                                if (! $record) {
                                    return [];
                                }
                                return $record->permissions()
                                    ->whereIn('name', [
                                        'manage_global_ai_modifiers',
                                        'toggle_modifier_status',
                                    ])
                                    ->pluck('name')
                                    ->toArray();
                            })
                            ->columns([
                                'default' => 1,
                                'sm'      => 2,
                            ]),
                    ]),
                Section::make('SaaS Plans')
                    ->description('App\Filament\Resources\Plans\PlanResource')
                    ->collapsible()
                    ->compact()
                    ->schema([
                        CheckboxList::make('saas_plans_permissions')
                            ->hiddenLabel()
                            ->options([
                                'toggle_plan_status' => 'Toggle Plan Status',
                                'feature_saas_plans' => 'Feature SaaS Plans',
                            ])
                            ->formatStateUsing(function (?array $state, ?Model $record): array {
                                if (! $record) {
                                    return [];
                                }
                                return $record->permissions()
                                    ->whereIn('name', [
                                        'toggle_plan_status',
                                        'feature_saas_plans',
                                    ])
                                    ->pluck('name')
                                    ->toArray();
                            })
                            ->columns([
                                'default' => 1,
                                'sm'      => 2,
                            ]),
                    ]),
                Section::make('SaaS Coupons')
                    ->description('App\Filament\Resources\Coupons\CouponResource')
                    ->collapsible()
                    ->compact()
                    ->schema([
                        CheckboxList::make('saas_coupons_permissions')
                            ->hiddenLabel()
                            ->options([
                                'toggle_coupon_status' => 'Toggle Coupon Status',
                            ])
                            ->formatStateUsing(function (?array $state, ?Model $record): array {
                                if (! $record) {
                                    return [];
                                }
                                return $record->permissions()
                                    ->whereIn('name', [
                                        'toggle_coupon_status',
                                    ])
                                    ->pluck('name')
                                    ->toArray();
                            })
                            ->columns([
                                'default' => 1,
                                'sm'      => 2,
                            ]),
                    ]),
                Section::make('Notification Channels')
                    ->description('App\Filament\Pages\NotificationSettings')
                    ->collapsible()
                    ->compact()
                    ->schema([
                        CheckboxList::make('notification_channels_permissions')
                            ->hiddenLabel()
                            ->options([
                                'manage_mandatory_notification_channels' => 'Manage Mandatory Notification Channels',
                            ])
                            ->formatStateUsing(function (?array $state, ?Model $record): array {
                                if (! $record) {
                                    return [];
                                }
                                return $record->permissions()
                                    ->whereIn('name', [
                                        'manage_mandatory_notification_channels',
                                    ])
                                    ->pluck('name')
                                    ->toArray();
                            })
                            ->columns([
                                'default' => 1,
                                'sm'      => 2,
                            ]),
                    ]),
            ])->columns(2);

        return \Filament\Schemas\Components\Tabs::make('Permissions')
            ->contained()
            ->tabs($tabs)
            ->columnSpanFull();
    }
}
