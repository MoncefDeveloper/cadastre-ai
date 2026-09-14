<?php

declare(strict_types=1);

namespace App\Filament\Resources\AiModifiers;

use App\Filament\Resources\AiModifiers\Pages\ManageAiModifiers;
use App\Models\AiModifier;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AiModifierResource extends Resource
{
    protected static ?string $model = AiModifier::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-sparkles';

    protected static ?string $navigationLabel = 'AI Modifiers';

    protected static string|UnitEnum|null $navigationGroup = 'AI Copilot';

    protected static ?int $navigationSort = 2;

    /*
     |----------------------------------------------------------------------
     | Global Search Configuration (AI Tone & Prompt Modifier Lookup)
     |----------------------------------------------------------------------
     */
    protected static ?string $recordTitleAttribute = 'label';

    public static function getGloballySearchableAttributes(): array
    {
        return ['label', 'instruction'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var AiModifier $record */
        return [
            'Scope' => $record->user_id ? "Agent: {$record->user?->name}" : 'Global Shortcut',
            'Tone'  => ucfirst($record->color ?? 'primary'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = AiModifier::where('is_active', true)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active tone modifiers and prompt rewrite rules';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Modifier Configuration')
                    ->description('Define button text, prompt instructions, and agent authorization scope.')
                    ->headerActions([
                        Action::make('autofill')
                            ->label('Quick Fill')
                            ->icon('heroicon-m-sparkles')
                            ->outlined()
                            ->color('warning')
                            ->action(function (callable $set): void {
                                $rand = rand(100, 999);
                                $set('label', "Bespoke Executive Tone #{$rand}");
                                $set('instruction', 'Rewrite the draft with an executive, highly diplomatic real estate vocabulary. Emphasize architectural pedigree and discrete confidentiality.');
                                $set('color', 'primary');
                                $set('is_active', true);
                            }),
                    ])
                    ->schema([
                        TextInput::make('label')
                            ->label('Modifier Label')
                            ->prefixIcon('heroicon-m-sparkles')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Make it Shorter')
                            ->validationMessages([
                                'required' => 'Please enter a clear label for this modifier button.',
                                'max' => 'The modifier label cannot exceed 255 characters.',
                            ]),

                        Textarea::make('instruction')
                            ->label('AI Rewrite Instruction')
                            ->required()
                            ->maxLength(1000)
                            ->rows(4)
                            ->placeholder('e.g., Rewrite the draft to be strictly under 50 words. Focus exclusively on price and viewing availability.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Please provide the prompt rewrite instructions for the AI.',
                                'max' => 'The instruction prompt cannot exceed 1,000 characters.',
                            ]),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->label('Agent Specific (Optional)')
                            ->placeholder('Global Modifier (All Agents)')
                            ->helperText('Leave unassigned to make this shortcut accessible to all agents agency-wide.')
                            ->default(fn () => auth()->id())
                            ->disabled(fn (): bool => ! auth()->user()->can('manage_global_ai_modifiers'))
                            ->dehydrated(true),

                        Select::make('color')
                            ->label('Tone Badge Palette')
                            ->options([
                                'primary' => 'Primary (Carmine)',
                                'success' => 'Success (Emerald)',
                                'warning' => 'Warning (Amber)',
                                'danger'  => 'Danger (Coral)',
                                'gray'    => 'Gray (Neutral)',
                            ])
                            ->default('primary')
                            ->required()
                            ->native(false)
                            ->validationMessages([
                                'required' => 'Please select a visual tone badge color.',
                            ]),

                        Toggle::make('is_active')
                            ->label('Active Status')
                            ->default(true)
                            ->disabled(
                                fn (?AiModifier $record): bool =>
                                $record !== null &&
                                $record->user_id !== auth()->id() &&
                                ! auth()->user()->can('toggle_modifier_status')
                            ),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn (Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->emptyStateHeading('No AI modifiers found')
            ->emptyStateDescription('Create quick-action prompt modifiers for the AI copilot.')
            ->emptyStateIcon('heroicon-o-sparkles')
            ->columns([
                // 1. Label (Max 30 Chars + Hover Tooltip)
                TextColumn::make('label')
                    ->label('Modifier Label')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (AiModifier $record): ?string => $record->label),

                // 2. Prompt Instruction (Max 30 Chars + Tooltip + Toggleable)
                TextColumn::make('instruction')
                    ->label('AI Prompt Instruction')
                    ->limit(30)
                    ->tooltip(fn (AiModifier $record): ?string => $record->instruction)
                    ->toggleable(),

                // 3. Scope (Centered + Tooltip + Toggleable)
                TextColumn::make('user.name')
                    ->label('Scope')
                    ->default('Global')
                    ->alignCenter()
                    ->tooltip(fn (AiModifier $record): string => $record->user_id ? "Agent: {$record->user?->name}" : 'Global Shortcut')
                    ->toggleable(),

                // 4. Tone Badge (Centered + Toggleable)
                TextColumn::make('color')
                    ->label('Tone')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (string $state): string => $state)
                    ->toggleable(),

                // 5. Active Status Toggle (Centered + Toggleable)
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->alignCenter()
                    ->sortable()
                    ->disabled(
                        fn (AiModifier $record): bool =>
                        $record->user_id !== auth()->id() &&
                            ! auth()->user()->can('toggle_modifier_status')
                    )
                    ->toggleable(),

                // 6. Creation Timestamp (Centered Gray Badge with Exact Datetime Tooltip)
                TextColumn::make('created_at')
                    ->label('Created')
                    ->badge()
                    ->color('gray')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (AiModifier $record): ?string => $record->created_at?->format('M d, Y - h:i A'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->recordActions([
                // Edit Action: Outlined info button
                EditAction::make()
                    ->modalWidth('lg')
                    ->color('info')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm')
                    ->visible(
                        fn (AiModifier $record): bool =>
                        $record->user_id === auth()->id() ||
                            auth()->user()->can('manage_global_ai_modifiers')
                    ),

                // Delete Action: Outlined primary button
                DeleteAction::make()
                    ->color('primary')
                    ->icon('heroicon-o-trash')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm')
                    ->visible(
                        fn (AiModifier $record): bool =>
                        $record->user_id === auth()->id() ||
                            auth()->user()->can('manage_global_ai_modifiers')
                    ),
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
            'index' => ManageAiModifiers::route('/'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user']);
    }
}
