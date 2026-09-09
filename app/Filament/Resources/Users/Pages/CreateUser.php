<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

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
                    $agentRoleId = Role::where('name', 'Senior Agent')->value('id');

                    $this->form->fill([
                        'name' => 'Yanis Bouzid',
                        'email' => "yanis.bouzid{$rand}@cadastre.test",
                        'phone' => '+213 550 11 22 33',
                        'password' => 'password',
                        'is_active' => true,
                        'roles' => $agentRoleId ? [$agentRoleId] : [],
                    ]);

                    Notification::make()
                        ->title('User Sample Data Filled!')
                        ->body('Profile information and Senior Agent role assigned.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
