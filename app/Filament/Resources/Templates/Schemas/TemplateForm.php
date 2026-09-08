<?php

declare(strict_types=1);

namespace App\Filament\Resources\Templates\Schemas;

use App\Enums\Thread\ThreadChannel;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Template Details')->schema([
                    TextInput::make('name')
                        ->required()
                        ->placeholder('e.g., Initial Buyer Inquiry'),

                    Select::make('channel')
                        ->options(ThreadChannel::class)
                        ->default(ThreadChannel::EMAIL)
                        ->required(),

                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->label('Specific Category (Optional)')
                        ->helperText('Leave empty if this is a global template.'),

                    Toggle::make('is_active')
                        ->default(true)
                        ->disabled(fn(): bool => ! auth()->user()->can('toggle_template_status')),
                ])->columns(2),

                Section::make('AI Brain (Strict Instructions)')->schema([
                    TagsInput::make('variables')
                        ->label('Required Variables')
                        ->placeholder('Press enter to add')
                        ->helperText('Define placeholders used in your prompt (e.g., client_name, budget). The system will strictly validate these before calling the AI.')
                        ->columnSpanFull(),
                    KeyValue::make('rules')
                        ->label('Execution Rules')
                        ->keyLabel('Condition / Rule')
                        ->valueLabel('Value')
                        // ->placeholder('e.g., min_budget, new_client_only')
                        ->helperText('Define specific rules for when this template should be used.')
                        ->columnSpanFull(),

                    MarkdownEditor::make('system_instructions')
                        ->label('System Identity & Instructions')
                        ->helperText('How should the AI behave? e.g., "You are an elite Parisian Real Estate Agent. Be extremely polite and formal."')
                        // ->fileAttachmentsDisk()
                        ->columnSpanFull(),

                    MarkdownEditor::make('prompt')
                        ->label('Drafting Prompt')
                        ->helperText('What should the AI write? e.g., "Draft an email to {{client_name}} thanking them for their interest..."')
                        ->required()
                        // ->fileAttachmentsDisk()
                        ->columnSpanFull(),
                ]),
            ]);
    }
}
