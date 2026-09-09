<?php

declare(strict_types=1);

namespace App\Filament\Pages\Concerns;

use App\DTOs\Email\PostmarkInboundDTO;
use App\Jobs\ProcessInboundEmailJob;
use Filament\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;

trait ManagesInboundSimulator
{
    public ?array $simulatorData = [];

    public function simulatorForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Inbound Webhook Payload')
                    ->description('Customize the incoming webhook data to test real-time AI parsing and inventory matching.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Client Full Name')
                            ->required()
                            ->placeholder('e.g. Dr. Malik Mansoor'),

                        TextInput::make('email')
                            ->label('Client Email Address')
                            ->email()
                            ->required()
                            ->placeholder('info+custom@moncefdev.me')
                            ->helperText('⚠️ Deliverability Warning: Please use a real email address (e.g. Gmail/Outlook) so test outbound replies deliver without Postmark bounce penalties.'),

                        TextInput::make('subject')
                            ->label('Inbound Email Subject')
                            ->required()
                            ->placeholder('e.g. Inquiry: Luxury Waterfront Property with Immediate Acquisition')
                            ->columnSpanFull(),

                        MarkdownEditor::make('body')
                            ->label('Message Body (Simulated Inbound Text)')
                            ->placeholder('Type what the client is asking for (e.g., looking for a 5-bedroom villa in Hydra under $5M with a swimming pool)...')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                                'link',
                                'redo',
                                'undo',
                            ])
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->footerActions([
                        Action::make('dispatchWebhook')
                            ->label('Dispatch Inbound Webhook Payload')
                            ->icon('heroicon-m-paper-airplane')
                            ->color('primary')
                            ->outlined()
                            ->action('simulateCustomLead'),
                    ])
                    ->footerActionsAlignment(Alignment::Center)
                    ->columns(2),
            ])
            ->statePath('simulatorData');
    }

    public function initSimulatorForm(): void
    {
        $this->simulatorForm->fill([
            'name' => 'Dr. Malik Mansoor',
            'email' => 'info+custom@moncefdev.me',
            'subject' => 'Inquiry: Luxury Waterfront Property with Immediate Acquisition',
            // 👈 Rebranded to Cadastre Team
            'body' => "Hello Cadastre Team,\n\nWe are looking for a luxury waterfront villa with a private pool and sea view in Oran (Canastel) or Nice. Our maximum budget is $5,000,000.\n\nPlease share suitable listings and availability for an on-site private viewing.",
        ]);
    }

    public function simulatePreset(string $presetKey): void
    {
        $presets = [
            'hydra' => [
                'name' => 'Ambassadorial Office',
                'email' => 'info+hydra@moncefdev.me',
                'subject' => 'Diplomatic Residence Requirement — 6 Bedroom Compound in Hydra',
                // 👈 Rebranded to Cadastre Private Office
                'body' => "Dear Cadastre Private Office,\n\nOur delegation requires an ambassadorial villa in Hydra, Algiers with a minimum of 6 bedrooms, high-security perimeter walls, heated private swimming pool, and underground parking.\n\nOur capital budget allocation is up to $5,000,000. When can we coordinate an architectural inspection?",
            ],
            'paris' => [
                'name' => 'Nathalie Laurent',
                'email' => 'info+paris@moncefdev.me',
                'subject' => 'Avenue Montaigne Penthouse Acquisition — Paris 8th',
                'body' => "Bonjour,\n\nI am searching for an exclusive 4-bedroom Haussmannian corner penthouse in the Golden Triangle (Paris 8th) with Eiffel Tower views and private elevator access. Budget allocation: €4,000,000.\n\nPlease share confidential off-market dossiers.",
            ],
            'miami' => [
                'name' => 'David Vance',
                'email' => 'info+miami@moncefdev.me',
                'subject' => 'Brickell Sky Penthouse Cash Acquisition ($3M Ceiling)',
                'body' => "Hello Agent,\n\nWe are reviewing luxury residential towers in Brickell Avenue, Miami. Seeking a high-floor penthouse with unobstructed Biscayne Bay views and a private rooftop plunge pool. We have $3,000,000 cash prepared for an expedited closing.",
            ],
        ];

        $preset = $presets[$presetKey] ?? null;

        if (! $preset) {
            return;
        }

        $this->dispatchInboundLead(
            name: $preset['name'],
            email: $preset['email'],
            subject: $preset['subject'],
            body: $preset['body'],
            presetName: ucfirst($presetKey) . ' Preset'
        );
    }

    public function simulateCustomLead(): void
    {
        $data = $this->simulatorForm->getState();

        $this->dispatchInboundLead(
            name: $data['name'],
            email: $data['email'],
            subject: $data['subject'],
            body: $data['body'],
            presetName: 'Custom Inquiry'
        );
    }

    private function dispatchInboundLead(string $name, string $email, string $subject, string $body, string $presetName): void
    {
        $uniqueMessageId = 'sim_' . uniqid() . '@moncefdev.me';

        $dto = new PostmarkInboundDTO(
            messageId: 'postmark_' . uniqid(),
            fromEmail: $email,
            fromName: $name,
            subject: $subject,
            textBody: strip_tags($body),
            htmlBody: '<p>' . nl2br(e($body)) . '</p>',
            mailboxHash: null,
            headers: [
                ['Name' => 'Message-ID', 'Value' => "<{$uniqueMessageId}>"],
            ],
            attachments: []
        );

        ProcessInboundEmailJob::dispatch($dto);

        $this->dispatch('close-modal', id: 'inbound-simulator-modal');

        Notification::make()
            ->title("{$presetName} Dispatched")
            ->body('Inbound webhook queued successfully. The AI is extracting criteria and calculating property matches.')
            ->success()
            ->send();
    }
}
