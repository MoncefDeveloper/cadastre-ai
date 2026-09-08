<?php

namespace App\Filament\Resources\Categories;

use App\Enums\Category\CategoryType;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\Action; // 👈 Filament v5 Unified Action
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
use Filament\Schemas\Components\Actions; // 👈 Filament v5 Schemas Component
use Filament\Schemas\Components\Utilities\Set; // 👈 Filament v5 Set Utility
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2) // 👈 Preserves your exact Image 1 2-column layout
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

                // Left Column: Name
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(
                        fn(string $operation, $state, callable $set) =>
                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                    ),

                // Right Column: Slug
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                // Left Column: Description
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

                // Right Column: Image Uploader
                FileUpload::make('icon')
                    ->label('Category Image / Icon')
                    ->image()
                    ->directory('category-icons')
                    ->maxSize(2048)
                    ->imageEditor()
                    // 🛡️ Lock image upload on baseline categories for visitors
                    ->disabled(fn(?Category $record): bool => auth()->id() !== 1 && $record !== null && $record->isBaselineRecord())
                    ->helperText(fn(?Category $record): ?string => auth()->id() !== 1 && $record?->isBaselineRecord() ? '🛡️ Baseline category icon is locked from modification in demo mode.' : null),

                // Left Column: Visibility
                Select::make('is_active')
                    ->label('Visibility Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ])
                    ->default(1)
                    ->required()
                    ->native(false)
                    ->selectablePlaceholder(false),

                // Right Column: Color
                ColorPicker::make('color'),

                // Left Column: Type
                Select::make('type')
                    ->options(CategoryType::class)
                    ->default(CategoryType::PROPERTY)
                    ->required()
                    ->live(),

                // Right Column: Parent Category
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
            ->checkIfRecordIsSelectableUsing(fn(\Illuminate\Database\Eloquent\Model $record): bool => ! method_exists($record, 'isBaselineRecord') || ! $record->isBaselineRecord() || auth()->id() === 1)
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                ImageColumn::make('icon')
                    ->label('Icon')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')),
                ColorColumn::make('color'),
                ToggleColumn::make('is_active')->sortable()->label('Active')
                    ->disabled(fn(): bool => ! auth()->user()->can('Update:Category')),
                TextColumn::make('type')->badge()->sortable(),
                TextColumn::make('parent.name')->label('Parent')->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
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
            'index' => ManageCategories::route('/'),
        ];
    }
}
