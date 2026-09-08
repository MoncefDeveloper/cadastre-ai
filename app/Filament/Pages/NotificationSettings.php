<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\NotificationType;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use UnitEnum;


class NotificationSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;
    use HasPageShield;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Notification Settings';
    protected static string | UnitEnum | null $navigationGroup = 'System';
    protected static ?string $title = 'Notification Settings';
    protected static ?string $slug = 'notification-settings';
    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.notification-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        // LAZY-SEEDING SAFE-GUARD
        $preference = $user->notificationPreferences()->firstOrCreate(
            ['notification_type' => NotificationType::NEW_MESSAGE],
            [
                'channel_database' => true,
                'channel_mail' => false,
                'channel_sms' => false,
                'channel_whatsapp' => false,
            ]
        );

        // HYDRATION
        $this->form->fill([
            NotificationType::NEW_MESSAGE->value => $preference->attributesToArray(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. UNIFIED PARENT SECTION
                Section::make('New Inbound Client Message')
                    ->description('Choose which channels are active when a client replies to an active thread.')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->collapsible()
                    ->schema([
                        Group::make()
                            ->statePath(NotificationType::NEW_MESSAGE->value)
                            ->schema([
                                // 2. CARD-IN-CARD NESTING (Using the helper method below)
                                $this->buildChannelCard(
                                    name: 'channel_database',
                                    title: 'In-App Notification',
                                    description: 'Receive alerts inside the dashboard bell.'
                                ),
                                $this->buildChannelCard(
                                    name: 'channel_mail',
                                    title: 'Email Notification',
                                    description: 'Receive alerts to your inbox.'
                                ),
                                $this->buildChannelCard(
                                    name: 'channel_sms',
                                    title: 'SMS Notification',
                                    description: 'Coming soon.',
                                    disabled: true
                                ),
                                $this->buildChannelCard(
                                    name: 'channel_whatsapp',
                                    title: 'WhatsApp Notification',
                                    description: 'Coming soon.',
                                    disabled: true
                                ),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    private function buildChannelCard(string $name, string $title, string $description, bool $disabled = false): Section
    {
        // Define your mandatory core channels
        $isMandatoryChannel = in_array($name, ['channel_database'], true);

        // If it is mandatory AND the agent lacks the override permission, lock it
        if ($isMandatoryChannel && ! auth()->user()->can('manage_mandatory_notification_channels')) {
            $disabled = true;
            $description = $description . ' (Mandatory Core Channel)';
        }

        return Section::make()
            ->compact()
            ->schema([
                Grid::make(12)
                    ->extraAttributes(['class' => 'items-center'])
                    ->schema([
                        TextEntry::make("{$name}_text")
                            ->hiddenLabel()
                            ->default($title)
                            ->formatStateUsing(fn(): HtmlString => new HtmlString("
                            <div class=\"flex flex-col text-left\">
                                <span class=\"text-sm font-bold text-gray-900 dark:text-white\">{$title}</span>
                                <span class=\"text-xs text-gray-500 dark:text-gray-400 mt-0.5\">{$description}</span>
                            </div>
                        "))
                            ->columnSpan(['default' => 12, 'sm' => 11]),

                        Toggle::make($name)
                            ->hiddenLabel()
                            ->disabled($disabled)
                            // Force dehydration so the disabled true state is still sent to save()
                            ->dehydrated(true)
                            ->columnSpan(['default' => 12, 'sm' => 1])
                            ->extraAttributes(['class' => 'flex sm:justify-end mt-2 sm:mt-0']),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $user = auth()->user();

        foreach ($state as $typeString => $channels) {
            $enumType = NotificationType::tryFrom($typeString);

            if ($enumType) {
                // BACKEND SECURITY INTERCEPTOR:
                // If they lack permission, override the array and force channel_database to true!
                if (! $user->can('manage_mandatory_notification_channels')) {
                    $channels['channel_database'] = true;
                }

                $user->notificationPreferences()->updateOrCreate(
                    ['notification_type' => $enumType],
                    $channels
                );
            }
        }

        Notification::make()
            ->title('Preferences saved successfully!')
            ->success()
            ->send();
    }
}
