<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        // Delete action completely hidden from edit page as requested
        return [];
    }

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        // Strict Ghost Admin Invariant: Master Root ID 1 yields 404 for non-root users
        if (auth()->id() !== 1 && (int) $this->getRecord()->getKey() === 1) {
            abort(404);
        }
    }
}
