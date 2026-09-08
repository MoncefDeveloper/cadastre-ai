<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\Plan;
use App\Models\Property;
use App\Models\Template;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Console\Command;

class DemoStatusCommand extends Command
{
    protected $signature = 'demo:status';

    protected $description = 'Displays live audit of all records, age relative to 30m cutoff, and predicted cleanup actions.';

    public function handle(): int
    {
        $cutoff = now()->subMinutes((int) config('sandbox.cleanup_interval_minutes', 30));

        $this->info("🕒 Live Sandbox Inspection (Cutoff Threshold: {$cutoff->format('H:i:s')})");
        $this->newLine();

        $models = [
            'Properties'   => ['class' => Property::class, 'limit' => config('sandbox.limits.properties', 10), 'name_col' => 'title'],
            'Categories'   => ['class' => Category::class, 'limit' => config('sandbox.limits.categories', 10), 'name_col' => 'name'],
            'Clients'      => ['class' => Client::class, 'limit' => config('sandbox.limits.clients', 10), 'name_col' => 'first_name'],
            'Threads'      => ['class' => Thread::class, 'limit' => config('sandbox.limits.threads', 6), 'name_col' => 'subject'],
            'Templates'    => ['class' => Template::class, 'limit' => config('sandbox.limits.templates', 10), 'name_col' => 'name'],
            'Plans'        => ['class' => Plan::class, 'limit' => config('sandbox.limits.plans', 3), 'name_col' => 'name'],
            'Coupons'      => ['class' => Coupon::class, 'limit' => config('sandbox.limits.coupons', 5), 'name_col' => 'code'],
            'Faqs'         => ['class' => Faq::class, 'limit' => config('sandbox.limits.faqs', 6), 'name_col' => 'question'],
            'Contacts'     => ['class' => Contact::class, 'limit' => config('sandbox.limits.contacts', 15), 'name_col' => 'name'],
            'Users'        => ['class' => User::class, 'limit' => config('sandbox.limits.users', 7), 'name_col' => 'email'],
        ];

        foreach ($models as $entityName => $config) {
            $modelClass = $config['class'];
            $limit      = (int) $config['limit'];
            $nameCol    = $config['name_col'];

            $records = $modelClass::orderBy('id')->get();

            if ($records->isEmpty()) {
                continue;
            }

            $rows = [];
            foreach ($records as $record) {
                $isBaseline = (int) $record->id <= $limit;
                $createdAt  = $record->created_at;
                $updatedAt  = $record->updated_at;

                $createdAgeMinutes = $createdAt ? (int) $createdAt->diffInMinutes(now()) : 0;

                // Predict action by demo:cleanup
                if ($isBaseline) {
                    $status = '<fg=cyan>BASELINE (Protected)</>';
                    $action = '<fg=green>✅ PRISTINE / SELF-HEAL</>';
                } else {
                    $status = '<fg=yellow>VISITOR (Test Record)</>';
                    if ($createdAt && $createdAt->lt($cutoff)) {
                        $action = '<fg=red>🛑 WILL PURGE (Age >= 30m)</>';
                    } else {
                        $action = "<fg=green>🟢 GRACE WINDOW (Age: {$createdAgeMinutes}m < 30m)</>";
                    }
                }

                $identifier = str((string) ($record->{$nameCol} ?? ''))->limit(35)->toString();

                $rows[] = [
                    "#{$record->id}",
                    $identifier,
                    $status,
                    $createdAt ? "{$createdAt->format('H:i:s')} ({$createdAgeMinutes}m ago)" : 'N/A',
                    $action,
                ];
            }

            $this->comment("📦 {$entityName} (Baseline Limit: <= {$limit})");
            $this->table(['ID', 'Identifier', 'Type', 'Created At', 'Predicted Cleanup Action'], $rows);
            $this->newLine();
        }

        return self::SUCCESS;
    }
}
