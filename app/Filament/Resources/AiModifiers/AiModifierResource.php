<?php

namespace App\Filament\Resources\AiModifiers;

use App\Filament\Resources\AiModifiers\Pages\ManageAiModifiers;
use App\Models\AiModifier;
use BackedEnum;
use Filament\Actions\Action; // 👈 Import Action
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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AiModifierResource extends Resource
{
    protected static ?string $model = AiModifier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Modifier Configuration')
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
                            ->required()
                            ->placeholder('e.g., Make it Shorter'),

                        Textarea::make('instruction')
                            ->placeholder('e.g., Rewrite the draft to be less than 50 words.')
                            ->columnSpanFull(),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Agent Specific (Optional)')
                            ->helperText('Leave empty to make this a Global button for everyone.')
                            ->default(fn () => auth()->id())
                            ->disabled(fn (): bool => ! auth()->user()->can('manage_global_ai_modifiers'))
                            ->dehydrated(true),

                        Select::make('color')
                            ->options([
                                'primary' => 'Primary (Amber)',
                                'success' => 'Success (Green)',
                                'warning' => 'Warning (Orange)',
                                'danger' => 'Danger (Red)',
                                'gray' => 'Gray',
                            ])
                            ->default('primary')
                            ->required(),

                        Toggle::make('is_active')
                            ->default(true),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn (\Illuminate\Database\Eloquent\Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->columns([
                TextColumn::make('label')->searchable(),
                TextColumn::make('instruction')->limit(50),
                TextColumn::make('user.name')->label('Scope')->default('Global'),
                TextColumn::make('color')->badge()->color(fn(string $state): string => $state),

                ToggleColumn::make('is_active')
                    ->disabled(fn (AiModifier $record): bool =>
                        $record->user_id !== auth()->id() &&
                        ! auth()->user()->can('toggle_modifier_status')
                    ),
            ])
            ->reorderable('sort_order')
            ->filters([])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('md')
                    ->visible(fn (AiModifier $record): bool =>
                        $record->user_id === auth()->id() ||
                        auth()->user()->can('manage_global_ai_modifiers')
                    ),
                DeleteAction::make()
                    ->visible(fn (AiModifier $record): bool =>
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
}
