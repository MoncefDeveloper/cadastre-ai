<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

trait ProtectsBaseline
{
    public static function bootProtectsBaseline(): void
    {
        static::deleting(function (Model $model): bool {
            // 1. Never block Artisan console commands (demo:cleanup, db:seed)
            if (app()->runningInConsole()) {
                return true;
            }

            // 2. Never block Root Master Admin (User ID 1)
            if (auth()->check() && auth()->id() === 1) {
                return true;
            }

            // 3. Resolve baseline limit dynamically from config/sandbox.php
            $tableName = $model->getTable();
            $limit = config("sandbox.limits.{$tableName}");

            // 4. Intercept & halt deletion on baseline records (id <= limit)
            if ($limit !== null && $model->getKey() !== null && (int) $model->getKey() <= (int) $limit) {
                Notification::make()
                    ->warning()
                    ->title('🛡️ Sandbox Protected Record')
                    ->body('Baseline demo records cannot be deleted. Create a new record to test deletion.')
                    ->send();

                return false; // 🛑 Aborts Eloquent delete transaction
            }

            return true;
        });
    }

    /**
     * Helper to check if the current model instance is a baseline seed record.
     */
    public function isBaselineRecord(): bool
    {
        $tableName = $this->getTable();
        $limit = config("sandbox.limits.{$tableName}");

        return $limit !== null && $this->getKey() !== null && (int) $this->getKey() <= (int) $limit;
    }
}
