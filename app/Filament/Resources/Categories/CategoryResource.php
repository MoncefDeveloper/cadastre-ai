<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories;

use App\Enums\Category\CategoryType;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-tag';

    protected static ?string $navigationLabel = 'Categories';

    protected static string|UnitEnum|null $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = Category::where('is_active', true)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'gray';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active listing and prompt category taxonomy';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Actions::make([
                    Action::make('quickFill')
                        ->label('Quick Fill')
                        ->icon('heroicon-m-sparkles')
                        ->outlined()
                        ->color('warning')
                        ->action(function (Set $set): void {
                            $rand = rand(100, 999);
                            $name = "Coastal Luxury Estates #{$rand}";

                            $set('name', $name);
                            $set('slug', Str::slug($name));
                            $set('description', "### Premier Coastal Residences\n\nExclusive high-end villas and seaside residences located along prestigious coastal corridors with private beach access and panoramic Mediterranean views.");
                            $set('icon', 'category-icons/category_placeholder (4).png');
                            $set('is_active', 1);
                            $set('color', '#0ea5e9');
                            $set('type', CategoryType::PROPERTY);
                            $set('parent_id', 1);
                        }),
                ])
                    ->alignRight()
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(
                        fn(string $operation, $state, callable $set) =>
                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                    )
                    ->validationMessages([
                        'required' => 'Please enter a unique category name.',
                        'max' => 'The category name cannot exceed 255 characters.',
                    ]),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'required' => 'A unique URL slug is required.',
                        'unique' => 'This URL slug is already in use by another category.',
                    ]),

                MarkdownEditor::make('description')
                    ->label('Category Narrative / Description')
                    ->placeholder('Enter rich details about this category...')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'bulletList',
                        'orderedList',
                        'link',
                    ]),

                FileUpload::make('icon')
                    ->label('Category Image / Icon')
                    ->image()
                    ->directory('category-icons')
                    ->maxSize(2048)
                    ->imageEditor()
                    ->disabled(fn(?Category $record): bool => auth()->id() !== 1 && $record !== null && $record->isBaselineRecord())
                    ->helperText(fn(?Category $record): ?string => auth()->id() !== 1 && $record?->isBaselineRecord() ? '🛡️ Baseline category icon is locked from modification in demo mode.' : null),

                Select::make('is_active')
                    ->label('Visibility Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ])
                    ->default(1)
                    ->required()
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->validationMessages([
                        'required' => 'Please select whether this category is active or inactive.',
                    ]),

                ColorPicker::make('color'),

                Select::make('type')
                    ->options(CategoryType::class)
                    ->default(CategoryType::PROPERTY)
                    ->required()
                    ->live()
                    ->validationMessages([
                        'required' => 'Please designate the taxonomy type (Property, Template, or Client Tag).',
                    ]),

                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Parent Category')
                    ->helperText('Optional: Select a parent if this is a subcategory.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No categories found')
            ->emptyStateDescription('Create a category to classify properties, templates, or client tags.')
            ->emptyStateIcon('heroicon-o-tag')
            ->columns([
                // 1. Name with 30-char limit, unclipped tooltip & toggleable
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn(Category $record): ?string => $record->name),

                // 2. Slug (Toggleable)
                TextColumn::make('slug')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn(Category $record): ?string => $record->slug)
                    ->toggleable(isToggledHiddenByDefault: true),

                // 3. Icon (Centered with Spacing Gutter & Toggleable)
                ImageColumn::make('icon')
                    ->label('Icon')
                    ->circular()
                    ->alignCenter()
                    ->extraHeaderAttributes(['class' => 'px-4'])
                    ->extraCellAttributes(['class' => 'px-4'])
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->toggleable(),

                // 4. Color Chip (Centered with Spacing Gutter & Toggleable)
                ColorColumn::make('color')
                    ->alignCenter()
                    ->extraHeaderAttributes(['class' => 'px-4'])
                    ->extraCellAttributes(['class' => 'px-4'])
                    ->toggleable(),

                // 5. Active Toggle (Centered with Spacing Gutter & Toggleable)
                ToggleColumn::make('is_active')
                    ->sortable()
                    ->label('Active')
                    ->alignCenter()
                    ->extraHeaderAttributes(['class' => 'px-4'])
                    ->extraCellAttributes(['class' => 'px-4'])
                    ->disabled(fn(): bool => ! auth()->user()->can('Update:Category'))
                    ->toggleable(),

                // 6. Type Badge (Centered with Spacing Gutter & Toggleable)
                TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['class' => 'px-4'])
                    ->extraCellAttributes(['class' => 'px-4'])
                    ->toggleable(),

                // 7. Clickable Parent Category (Opens Parent Edit Modal directly & Toggleable)
                TextColumn::make('parent.name')
                    ->label('Parent')
                    ->sortable()
                    ->alignCenter()
                    ->placeholder('—')
                    ->color(fn(Category $record): ?string => $record->parent_id ? 'primary' : null)
                    ->tooltip(fn(Category $record): ?string => $record->parent ? "Click to inspect {$record->parent->name}" : null)
                    ->action(
                        fn(Category $record, $livewire) => $record->parent_id
                            ? $livewire->mountTableAction('edit', (string) $record->parent_id)
                            : null
                    )
                    ->extraAttributes(fn(Category $record): array => $record->parent_id ? ['class' => 'underline'] : [])
                    ->toggleable(),

                // 8. Creation Timestamp (Centered, Toggleable, hidden by default)
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip(fn(Category $record): ?string => $record->created_at?->format('M d, Y - h:i A')),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(CategoryType::class)
                    ->label('Category Type'),
            ])
            ->recordActions([
                EditAction::make()
                    ->color('info')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->iconSize('sm'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->outlined()
                    ->button()
                    ->color('primary')
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
            'index' => ManageCategories::route('/'),
        ];
    }
}
