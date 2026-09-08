<?php

declare(strict_types=1);

namespace App\Filament\Resources\Templates\Pages;

use App\Enums\Thread\ThreadChannel;
use App\Filament\Resources\Templates\TemplateResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTemplate extends CreateRecord
{
    protected static string $resource = TemplateResource::class;

    /**
     * 🔄 Redirect directly back to the Templates index table after creation
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * ⚡ Quick Fill Header Action for Testing
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('quickFill')
                ->label('⚡ Quick Fill')
                ->icon('heroicon-m-sparkles')
                ->outlined()
                ->color('warning')
                ->action(function (): void {
                    $rand = rand(100, 999);

                    $this->form->fill([
                        // Section 1: Template Details
                        'name' => "VIP Penthouse Proposal #{$rand}",
                        'channel' => ThreadChannel::EMAIL,
                        'category_id' => 6, // Lead Inquiries & First Contact
                        'is_active' => true,

                        // Section 2: AI Brain
                        'variables' => [
                            'client_name',
                            'target_city',
                            'property_title',
                            'property_price',
                            'agent_name',
                        ],
                        'rules' => [
                            'tone' => 'luxury_persuasive',
                            'max_words' => '160',
                            'fha_compliant' => 'strict',
                        ],
                        'system_instructions' => "You are an elite private real estate advisor representing high-net-worth investors across Paris, Algiers, and Miami. Be impeccably polite, warm, and discreet.",
                        'prompt' => "Draft a personalized email to {{client_name}} presenting the luxury property {{property_title}} in {{target_city}} priced at {{property_price}}.\n\nHighlight the architectural pedigree, private finishes, and invite them for a private confidential viewing with {{agent_name}}.",
                    ]);

                    Notification::make()
                        ->title('Template Sample Data Filled!')
                        ->body('All AI prompt variables and system rules populated.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
