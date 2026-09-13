<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients\Pages;

use App\Enums\ClientStatus;
use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('quickFill')
                ->label('Quick Fill')
                ->icon('heroicon-m-sparkles')
                ->outlined()
                ->color('warning')
                ->action(function (): void {
                    $rand = rand(100, 999);

                    $this->form->fill([
                        'first_name' => 'Amine',
                        'last_name' => 'Bensalem',
                        'email' => "amine.bensalem{$rand}@diaspora-invest.test",
                        'phone' => '+213 550 88 44 22',
                        'status' => ClientStatus::ACTIVE,
                        'source' => 'VIP Private Referral',
                    ]);

                    Notification::make()
                        ->title('Client Sample Data Filled!')
                        ->body('Ready for immediate submission.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
