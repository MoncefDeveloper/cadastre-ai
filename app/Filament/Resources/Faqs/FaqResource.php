<?php

declare(strict_types=1);

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Models\Faq;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'question';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Question Details')
                    ->headerActions([
                        // ⚡ Quick Fill Action (100% Non-destructive)
                        Action::make('quickFill')
                            ->label('⚡ Quick Fill')
                            ->icon('heroicon-m-sparkles')
                            ->outlined()
                            ->color('warning')
                            ->action(function (Set $set): void {
                                $rand = rand(100, 999);

                                $set('question', "How does the AI handle multilingual inquiries in French and Arabic? #{$rand}");
                                $set('target_audience', 'agents');
                                $set('is_active', true);
                                $set('answer', '<p>Cadastre AI automatically parses inbound text using <strong>Google Gemini</strong> language intelligence, extracting real estate parameters regardless of whether the client writes in French, Arabic, or English.</p>');
                            }),
                    ])
                    ->columns(2)
                    ->components([
                        TextInput::make('question')
                            ->columnSpanFull()
                            ->required()
                            ->maxLength(255),

                        Select::make('target_audience')
                            ->options([
                                'global' => 'Global (Public)',
                                'agents' => 'Agents Only',
                                'clients' => 'Clients Only',
                                'billing' => 'Billing / Subscriptions',
                            ])
                            ->default('global')
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Visible')
                            ->default(true)
                            ->inline(false),
                    ]),

                Section::make('Answer')
                    ->components([
                        RichEditor::make('answer')
                            ->hiddenLabel()
                            ->required()
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->recordTitleAttribute('question')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('question')
                    ->searchable()
                    ->limit(50)
                    ->weight('bold'),

                TextColumn::make('target_audience')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'global' => 'success',
                        'agents' => 'warning',
                        'clients' => 'info',
                        'billing' => 'danger',
                        default => 'gray',
                    }),

                ToggleColumn::make('is_active')->label('Active')->sortable()->alignCenter()
                    ->disabled(fn(Faq $record): bool => ! auth()->user()->can('Update:Faq') || $record->id === auth()->id()),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status'),
                SelectFilter::make('target_audience')
                    ->options([
                        'global' => 'Global',
                        'agents' => 'Agents',
                        'clients' => 'Clients',
                        'billing' => 'Billing',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver()
                    ->color('gray'), // Quiet neutral link (illuminates to white on hover)
                DeleteAction::make(), // Blazing Flame Vermilion (#F95428)
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
            'index' => ManageFaqs::route('/'),
        ];
    }
}
