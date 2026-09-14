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
                // SECTION 1: Identity & Routing Configuration
                Section::make('Template Details')
                    ->description('Blueprint identity, delivery channel, and category assignment.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Template Name')
                            ->prefixIcon('heroicon-m-document-duplicate')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., VIP Penthouse Proposal')
                            ->validationMessages([
                                'required' => 'Please provide a descriptive name for this AI prompt blueprint.',
                                'max' => 'The template name cannot exceed 255 characters.',
                            ]),

                        Select::make('channel')
                            ->label('Delivery Channel')
                            ->options(ThreadChannel::class)
                            ->default(ThreadChannel::EMAIL)
                            ->required()
                            ->native(false)
                            ->validationMessages([
                                'required' => 'Please specify which communication channel this template is designed for.',
                            ]),

                        Select::make('category_id')
                            ->label('Category Scope (Optional)')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Global Template')
                            ->helperText('Leave empty if this template should be available across all categories.'),

                        Toggle::make('is_active')
                            ->label('Active Status')
                            ->default(true)
                            ->disabled(fn (): bool => ! auth()->user()->can('toggle_template_status')),
                    ])
                    ->columns(2),

                // SECTION 2: The AI Brain (Prompts & Behavioral Rules)
                Section::make('AI Brain (Strict Prompt Engineering)')
                    ->description('Define variables, contextual behavior, and generation instructions.')
                    ->schema([
                        TagsInput::make('variables')
                            ->label('Required Variables')
                            ->placeholder('Press enter to add variable token')
                            ->helperText('Define placeholders used in your prompt (e.g. client_name, property_title). The system strictly validates these before invoking Gemini AI.')
                            ->columnSpanFull(),

                        KeyValue::make('rules')
                            ->label('Execution Rules')
                            ->keyLabel('Condition / Parameter')
                            ->valueLabel('Target Value')
                            ->helperText('Define operational rules for when this template applies (e.g., Key: "tone", Value: "luxury_persuasive").')
                            ->columnSpanFull(),

                        MarkdownEditor::make('system_instructions')
                            ->label('System Identity & Persona')
                            ->placeholder('e.g., You are an elite Parisian private real estate advisor representing high-net-worth investors...')
                            ->helperText('How should the AI behave? Defines tone boundaries, discretion, and role identity.')
                            ->disableToolbarButtons(['attachFiles'])
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                                'link',
                                'redo',
                                'undo',
                            ])
                            ->columnSpanFull(),

                        MarkdownEditor::make('prompt')
                            ->label('Drafting Prompt Blueprint')
                            ->placeholder('e.g., Draft a personalized introduction to {{client_name}} presenting {{property_title}}...')
                            ->helperText('What should the AI write? Instructions should reference your defined {{variables}}.')
                            ->required()
                            ->disableToolbarButtons(['attachFiles'])
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                                'link',
                                'redo',
                                'undo',
                            ])
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'The AI drafting prompt instructions are required.',
                            ]),
                    ]),
            ]);
    }
}
