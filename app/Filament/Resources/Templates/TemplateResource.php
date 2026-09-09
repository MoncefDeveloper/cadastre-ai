<?php

declare(strict_types=1);

namespace App\Filament\Resources\Templates;

use App\Filament\Resources\Templates\Pages\CreateTemplate;
use App\Filament\Resources\Templates\Pages\EditTemplate;
use App\Filament\Resources\Templates\Pages\ListTemplates;
use App\Filament\Resources\Templates\Schemas\TemplateForm;
use App\Filament\Resources\Templates\Tables\TemplatesTable;
use App\Models\Template;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-document-duplicate';

    protected static ?string $navigationLabel = 'AI Templates';

    protected static string|UnitEnum|null $navigationGroup = 'AI Copilot';

    protected static ?int $navigationSort = 1;

    /*
     |----------------------------------------------------------------------
     | Global Search Configuration (AI Prompt Blueprints Lookup)
     |----------------------------------------------------------------------
     */
    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'prompt'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Template $record */
        return [
            'Channel'  => $record->channel?->name ?? 'All',
            'Category' => $record->category?->name ?? 'Global Scope',
        ];
    }


    public static function getNavigationBadge(): ?string
    {
        $count = Template::where('is_active', true)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Published AI prompt blueprints ready for drafting';
    }

    public static function form(Schema $schema): Schema
    {
        return TemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TemplatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTemplates::route('/'),
            'create' => CreateTemplate::route('/create'),
            'edit' => EditTemplate::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['category']);
    }
}
