<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\AiModifier;
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
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SandboxTopbarWidget extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions, InteractsWithSchemas;
    public bool $inspectorOpen = false;

    /**
     * ⚡ Ultra-Fast Aggregate Visitor Count (For the Topbar Pill)
     * Queries only total visitor counts (id > limit) without full table scans.
     */
    #[Computed]
    public function visitorRecordsCount(): int
    {
        $propertyLimit = (int) config('sandbox.limits.properties', 10);
        $categoryLimit = (int) config('sandbox.limits.categories', 10);
        $clientLimit   = (int) config('sandbox.limits.clients', 10);
        $threadLimit   = (int) config('sandbox.limits.threads', 6);
        $templateLimit = (int) config('sandbox.limits.templates', 10);
        $userLimit     = (int) config('sandbox.limits.users', 7);

        return Property::where('id', '>', $propertyLimit)->count()
            + Category::where('id', '>', $categoryLimit)->count()
            + Client::where('id', '>', $clientLimit)->count()
            + Thread::where('id', '>', $threadLimit)->count()
            + Template::where('id', '>', $templateLimit)->count()
            + User::where('id', '>', $userLimit)->count();
    }

    /**
     * 🧠 Lazy-Loaded Detailed Model Inspector
     * Only executes when the evaluator opens the slide-over modal.
     */
    #[Computed]
    public function inspectorData(): array
    {
        if (! $this->inspectorOpen) {
            return [
                'total_baseline' => 0,
                'total_modified' => 0,
                'total_grace' => 0,
                'total_expired' => 0,
                'models' => [],
            ];
        }

        $cutoff = now()->subMinutes((int) config('sandbox.cleanup_interval_minutes', 30));

        $modelsConfig = [
            'Properties'   => ['class' => Property::class, 'limit' => config('sandbox.limits.properties', 10), 'name' => 'title', 'icon' => 'heroicon-m-home-modern', 'resource' => \App\Filament\Resources\Properties\PropertyResource::class],
            'Categories'   => ['class' => Category::class, 'limit' => config('sandbox.limits.categories', 10), 'name' => 'name', 'icon' => 'heroicon-m-tag', 'resource' => \App\Filament\Resources\Categories\CategoryResource::class],
            'Clients'      => ['class' => Client::class, 'limit' => config('sandbox.limits.clients', 10), 'name' => 'first_name', 'icon' => 'heroicon-m-users', 'resource' => \App\Filament\Resources\Clients\ClientResource::class],
            'Threads'      => ['class' => Thread::class, 'limit' => config('sandbox.limits.threads', 6), 'name' => 'subject', 'icon' => 'heroicon-m-chat-bubble-left-right', 'url' => url('/admin/inbox')],
            'Templates'    => ['class' => Template::class, 'limit' => config('sandbox.limits.templates', 10), 'name' => 'name', 'icon' => 'heroicon-m-document-duplicate', 'resource' => \App\Filament\Resources\Templates\TemplateResource::class],
            'Plans'        => ['class' => Plan::class, 'limit' => config('sandbox.limits.plans', 3), 'name' => 'name', 'icon' => 'heroicon-m-credit-card', 'resource' => \App\Filament\Resources\Plans\PlanResource::class],
            'Coupons'      => ['class' => Coupon::class, 'limit' => config('sandbox.limits.coupons', 5), 'name' => 'code', 'icon' => 'heroicon-m-ticket', 'resource' => \App\Filament\Resources\Coupons\CouponResource::class],
            'Faqs'         => ['class' => Faq::class, 'limit' => config('sandbox.limits.faqs', 6), 'name' => 'question', 'icon' => 'heroicon-m-question-mark-circle', 'resource' => \App\Filament\Resources\Faqs\FaqResource::class],
            'Contacts'     => ['class' => Contact::class, 'limit' => config('sandbox.limits.contacts', 15), 'name' => 'name', 'icon' => 'heroicon-m-envelope', 'resource' => \App\Filament\Resources\Contacts\ContactResource::class],
            'Users'        => ['class' => User::class, 'limit' => config('sandbox.limits.users', 7), 'name' => 'email', 'icon' => 'heroicon-m-user-group', 'resource' => \App\Filament\Resources\Users\UserResource::class],
        ];

        $totalBaseline = 0;
        $totalModified = 0; // 👈 Track total modified baseline records
        $totalGrace = 0;
        $totalExpired = 0;
        $modelBreakdowns = [];

        foreach ($modelsConfig as $label => $cfg) {
            $class = $cfg['class'];
            $limit = (int) $cfg['limit'];
            $nameCol = $cfg['name'];
            $icon = $cfg['icon'];

            // 🛡️ GHOST ADMIN CONCEALMENT: Exclude User ID 1 for non-root visitors
            $query = $class::orderBy('id');
            if ($class === User::class && auth()->id() !== 1) {
                $query->where('id', '!=', 1);
            }

            $records = $query->get();
            $effectiveLimit = ($class === User::class && auth()->id() !== 1) ? $limit - 1 : $limit;
            // Log::info("Effective Limit: {$effectiveLimit}");

            $baselineCount = 0;
            $modifiedCount = 0;
            $graceCount = 0;
            $expiredCount = 0;
            $formattedRecords = [];

            $indexUrl = isset($cfg['resource']) ? $cfg['resource']::getUrl('index') : ($cfg['url'] ?? null);

            foreach ($records as $record) {
                $isBaseline = (int) $record->id <= $limit;
                $createdAt  = $record->created_at;
                $updatedAt  = $record->updated_at;
                $ageMinutes = $createdAt ? (int) $createdAt->diffInMinutes(now()) : 0;
                $updatedAgeMinutes = $updatedAt ? (int) $updatedAt->diffInMinutes(now()) : 0;
                $isModified = false;

                if ($isBaseline) {
                    $baselineCount++;
                    $totalBaseline++;

                    // 🔍 Detect if baseline record was updated
                    if ($updatedAt && $createdAt && $updatedAt->gt($createdAt)) {
                        $isModified = true;
                        $modifiedCount++;
                        $totalModified++;
                    }

                    $status = 'baseline';
                } else {
                    if ($createdAt && $createdAt->lt($cutoff)) {
                        $expiredCount++;
                        $totalExpired++;
                        $status = 'expired';
                    } else {
                        $graceCount++;
                        $totalGrace++;
                        $status = 'grace';
                    }
                }

                $recordUrl = null;
                if (isset($cfg['resource'])) {
                    try {
                        if ($cfg['resource']::hasPage('edit')) {
                            $recordUrl = $cfg['resource']::getUrl('edit', ['record' => $record->id]);
                        } else {
                            $recordUrl = $cfg['resource']::getUrl('index');
                        }
                    } catch (\Throwable $e) {
                        $recordUrl = $cfg['resource']::getUrl('index');
                    }
                } elseif (isset($cfg['url'])) {
                    $recordUrl = $cfg['url'];
                }

                $formattedRecords[] = [
                    'id' => $record->id,
                    'identifier' => str((string) ($record->{$nameCol} ?? ''))->limit(32)->toString(),
                    'status' => $status,
                    'is_modified' => $isModified,
                    'age_minutes' => $ageMinutes,
                    'updated_age_minutes' => $updatedAgeMinutes, // 👈 Pass this to the view
                    'url' => $recordUrl,
                ];
            }

            $modelBreakdowns[$label] = [
                'icon' => $icon,
                'limit' => $effectiveLimit, // 👈 Shows 6 for visitors, 7 for Root Admin
                'index_url' => $indexUrl,
                'baseline_count' => $baselineCount,
                'modified_count' => $modifiedCount, // 👈 For section header badge
                'grace_count' => $graceCount,
                'expired_count' => $expiredCount,
                'records' => $formattedRecords,
            ];
        }

        return [
            'total_baseline' => $totalBaseline,
            'total_modified' => $totalModified, // 👈 For top card
            'total_grace'    => $totalGrace,
            'total_expired'  => $totalExpired,
            'models'         => $modelBreakdowns,
        ];
    }

    public function openInspector(): void
    {
        $this->inspectorOpen = true;
        $this->dispatch('open-modal', id: 'sandbox-inspector-modal');
    }

    public function closeInspector(): void
    {
        $this->inspectorOpen = false;
        $this->dispatch('close-modal', id: 'sandbox-inspector-modal');
    }

    #[Computed]
    public function cleanupAction(): Action
    {
        return Action::make('cleanup')
            ->label('Run Cleanup Now')
            ->icon('heroicon-m-arrow-path')
            ->color('primary')
            ->outlined()
            ->size('md')
            ->requiresConfirmation()
            ->modalHeading('Execute Sandbox Self-Healing Engine?')
            ->modalDescription('This triggers the background 30-minute self-healing cleanup job. Expired visitor records (≥ 30m) and stale baseline edits will be purged, while fresh records (< 30m) will be preserved in their grace window.')
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalIconColor('warning')
            ->modalSubmitActionLabel('Yes, Run Cleanup')
            ->action(function (): void {
                $this->triggerManualCleanup();
            });
    }

    public function triggerManualCleanup(): void
    {
        Artisan::call('demo:cleanup');

        $this->closeInspector();

        Notification::make()
            ->title('Sandbox Maintenance Executed')
            ->body('Expired visitor records purged and baseline records restored to seed defaults.')
            ->success()
            ->send();
    }
    public function render(): View
    {
        return view('filament.components.sandbox-topbar-widget');
    }

    #[Computed]
    public function modifiedBaselineCount(): int
    {
        return Property::where('id', '<=', config('sandbox.limits.properties', 10))->whereColumn('updated_at', '>', 'created_at')->count()
            + Category::where('id', '<=', config('sandbox.limits.categories', 10))->whereColumn('updated_at', '>', 'created_at')->count()
            + Faq::where('id', '<=', config('sandbox.limits.faqs', 6))->whereColumn('updated_at', '>', 'created_at')->count()
            + Template::where('id', '<=', config('sandbox.limits.templates', 10))->whereColumn('updated_at', '>', 'created_at')->count()
            + AiModifier::where('id', '<=', config('sandbox.limits.ai_modifiers', 10))->whereColumn('updated_at', '>', 'created_at')->count()
            + Plan::where('id', '<=', config('sandbox.limits.plans', 3))->whereColumn('updated_at', '>', 'created_at')->count()
            + Coupon::where('id', '<=', config('sandbox.limits.coupons', 5))->whereColumn('updated_at', '>', 'created_at')->count()
            + Client::where('id', '<=', config('sandbox.limits.clients', 10))->whereColumn('updated_at', '>', 'created_at')->count();
    }

    /**
     * 🔢 Combined total of visitor-created records + modified baseline records
     */
    #[Computed]
    public function totalPendingActionsCount(): int
    {
        return $this->visitorRecordsCount + $this->modifiedBaselineCount;
    }

    /**
     * 🚦 Dynamic Lifecycle Status Color
     */
    #[Computed]
    public function statusColor(): string
    {
        $cutoff = now()->subMinutes((int) config('sandbox.cleanup_interval_minutes', 30));

        // 1. Red if any visitor record has expired (>= 30m)
        $hasExpired = Property::where('id', '>', config('sandbox.limits.properties', 10))->where('created_at', '<', $cutoff)->exists()
            || Category::where('id', '>', config('sandbox.limits.categories', 10))->where('created_at', '<', $cutoff)->exists()
            || Client::where('id', '>', config('sandbox.limits.clients', 10))->where('created_at', '<', $cutoff)->exists()
            || Thread::where('id', '>', config('sandbox.limits.threads', 6))->where('created_at', '<', $cutoff)->exists()
            || User::where('id', '>', config('sandbox.limits.users', 7))->where('created_at', '<', $cutoff)->exists();

        if ($hasExpired) {
            return 'danger';
        }

        // 2. Amber if new visitor records exist OR baseline records were modified
        if ($this->totalPendingActionsCount > 0) {
            return 'warning';
        }

        // 3. Green if 100% clean and pristine
        return 'success';
    }
}
