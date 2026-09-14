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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-question-mark-circle';

    protected static ?string $navigationLabel = 'FAQs';

    protected static string|UnitEnum|null $navigationGroup = 'Commercial';

    protected static ?int $navigationSort = 3;

    /*
     |----------------------------------------------------------------------
     | Global Search Configuration (Knowledge Base Lookup)
     |----------------------------------------------------------------------
     */
    protected static ?string $recordTitleAttribute = 'question';

    public static function getGloballySearchableAttributes(): array
    {
        return ['question', 'answer'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Faq $record */
        return [
            'Audience' => ucfirst($record->target_audience ?? 'global'),
            'Status'   => $record->is_active ? 'Visible' : 'Hidden',
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Faq::where('is_active', true)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'gray';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Published knowledge base and onboarding answers';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Question Details')
                    ->description('Knowledge base inquiry, target readership segment, and visibility.')
                    ->headerActions([
                        Action::make('quickFill')
                            ->label('Quick Fill') // Removed lightning emoji
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
                            ->label('Question')
                            ->prefixIcon('heroicon-m-question-mark-circle')
                            ->columnSpanFull()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., How does the AI match inquiries to properties?')
                            ->validationMessages([
                                'required' => 'Please enter the question text.',
                                'max' => 'The question cannot exceed 255 characters.',
                            ]),

                        Select::make('target_audience')
                            ->label('Target Audience')
                            ->options([
                                'global'  => 'Global (Public)',
                                'agents'  => 'Agents Only',
                                'clients' => 'Clients Only',
                                'billing' => 'Billing / Subscriptions',
                            ])
                            ->default('global')
                            ->required()
                            ->native(false)
                            ->validationMessages([
                                'required' => 'Please select the target audience segment.',
                            ]),

                        Toggle::make('is_active')
                            ->label('Visible in Knowledge Base')
                            ->default(true)
                            ->inline(false)
                            ->disabled(fn (?Faq $record): bool => $record !== null && ! auth()->user()->can('Update:Faq')),
                    ]),

                Section::make('Answer Content')
                    ->description('Detailed explanatory response supporting rich text formatting.')
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
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Please provide the answer content.',
                            ]),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn (Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->recordTitleAttribute('question')
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->emptyStateHeading('No FAQs found')
            ->emptyStateDescription('Create knowledge base entries and onboarding answers.')
            ->emptyStateIcon('heroicon-o-question-mark-circle')
            ->columns([
                // 1. Question (Max 30 Chars + Hover Tooltip)
                TextColumn::make('question')
                    ->label('Question')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn (Faq $record): ?string => $record->question)
                    ->weight('bold'),

                // 2. Target Audience Badge (Centered + Toggleable)
                TextColumn::make('target_audience')
                    ->label('Audience')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (string $state): string => match ($state) {
                        'global'  => 'success',
                        'agents'  => 'warning',
                        'clients' => 'info',
                        'billing' => 'danger',
                        default   => 'gray',
                    })
                    ->toggleable(),

                // 3. Active Status Toggle (Centered + Toggleable)
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->alignCenter()
                    ->disabled(fn (Faq $record): bool => ! auth()->user()->can('Update:Faq'))
                    ->toggleable(),

                // 4. Creation Timestamp (Centered Gray Badge with Exact Datetime Tooltip)
                TextColumn::make('created_at')
                    ->label('Created')
                    ->badge()
                    ->color('gray')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (Faq $record): ?string => $record->created_at?->format('M d, Y - h:i A'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status'),

                SelectFilter::make('target_audience')
                    ->options([
                        'global'  => 'Global',
                        'agents'  => 'Agents',
                        'clients' => 'Clients',
                        'billing' => 'Billing',
                    ])
                    ->label('Audience Segment'),
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
            'index' => ManageFaqs::route('/'),
        ];
    }
}
