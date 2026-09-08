<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AiModifier;
use App\Models\Category;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\Message;
use App\Models\Plan;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Template;
use App\Models\Thread;
use App\Models\ThreadPropertyMatch;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\AiModifierSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ClientSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\CouponSeeder;
use Database\Seeders\FaqSeeder;
use Database\Seeders\MessageSeeder;
use Database\Seeders\PlanSeeder;
use Database\Seeders\PropertySeeder;
use Database\Seeders\TemplateSeeder;
use Database\Seeders\ThreadPropertyMatchSeeder;
use Database\Seeders\ThreadSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;

class DemoCleanupCommand extends Command
{
    protected $signature = 'demo:cleanup';

    protected $description = 'Purges expired visitor test data and self-heals baseline records using a 30-minute sliding window.';

    public function handle(): int
    {
        $this->info('🛡️  Starting 30-Minute Sliding Sandbox Maintenance...');

        $cutoff = now()->subMinutes((int) config('sandbox.cleanup_interval_minutes', 30));
        $this->comment("   🕒 Sliding Cutoff: Any visitor data created before [{$cutoff->toDateTimeString()}] will be pruned.");

        $this->purgeStorageFiles($cutoff);
        $this->cascadeDeleteVisitorRecords($cutoff);
        $this->selfHealBaseline($cutoff); // 👈 Passes $cutoff to Stage 3
        $this->cleanupSessionsAndCache();

        $this->newLine();
        $this->info('🎉 Sliding Sandbox Self-Healing Maintenance Completed.');
        Log::info('[Sandbox Cleanup] Sliding self-healing cycle executed successfully.');

        return self::SUCCESS;
    }

    /**
     * Stage 1: Purge expired physical files from public storage
     */
    protected function purgeStorageFiles(Carbon $cutoff): void
    {
        $this->comment('1. Identifying expired visitor records & purging orphaned storage files...');

        $propertyLimit = (int) config('sandbox.limits.properties', 10);
        $imageLimit    = (int) config('sandbox.limits.property_images', 20);
        $categoryLimit = (int) config('sandbox.limits.categories', 10);
        $messageLimit  = (int) config('sandbox.limits.messages', 16);
        $threadLimit   = (int) config('sandbox.limits.threads', 6);

        $expiredPropertyIds = Property::where('id', '>', $propertyLimit)
            ->where('created_at', '<', $cutoff)
            ->pluck('id');

        $expiredThreadIds = Thread::where('id', '>', $threadLimit)
            ->where('created_at', '<', $cutoff)
            ->pluck('id');

        $expiredCategoryIds = Category::where('id', '>', $categoryLimit)
            ->where('created_at', '<', $cutoff)
            ->pluck('id');

        $protectedFiles = PropertyImage::where('property_id', '<=', $propertyLimit)
            ->where('id', '<=', $imageLimit)
            ->pluck('image_path')
            ->merge(
                Category::where('id', '<=', $categoryLimit)->whereNotNull('icon')->pluck('icon')
            )
            ->filter()
            ->unique()
            ->toArray();

        $filesToDelete = collect()
            ->merge(
                PropertyImage::whereIn('property_id', $expiredPropertyIds)
                    ->orWhere(fn($q) => $q->where('id', '>', $imageLimit)->where('created_at', '<', $cutoff))
                    ->pluck('image_path')
            )
            ->merge(
                Category::whereIn('id', $expiredCategoryIds)->whereNotNull('icon')->pluck('icon')
            )
            ->merge(
                Message::where(function ($q) use ($expiredThreadIds, $messageLimit, $cutoff) {
                    $q->whereIn('thread_id', $expiredThreadIds)
                        ->orWhere(fn($sq) => $sq->where('id', '>', $messageLimit)->where('created_at', '<', $cutoff));
                })
                    ->whereNotNull('attachments')
                    ->get()
                    ->flatMap(fn($msg) => collect($msg->attachments ?? [])->pluck('url'))
            )
            ->filter()
            ->unique()
            ->diff($protectedFiles);

        $purgedCount = 0;
        foreach ($filesToDelete as $filePath) {
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                $purgedCount++;
            }
        }

        $this->info("   ✅ Purged {$purgedCount} expired visitor files from storage.");
    }

    /**
     * Stage 2: Child-first MySQL cascading deletion
     */
    protected function cascadeDeleteVisitorRecords(Carbon $cutoff): void
    {
        $this->comment('2. Deleting expired visitor records from MySQL (Child-first cascade)...');

        $propertyLimit = (int) config('sandbox.limits.properties', 10);
        $imageLimit    = (int) config('sandbox.limits.property_images', 20);
        $categoryLimit = (int) config('sandbox.limits.categories', 10);
        $messageLimit  = (int) config('sandbox.limits.messages', 16);
        $threadLimit   = (int) config('sandbox.limits.threads', 6);
        $matchLimit    = (int) config('sandbox.limits.thread_property_matches', 8);
        $clientLimit   = (int) config('sandbox.limits.clients', 10);
        $contactLimit  = (int) config('sandbox.limits.contacts', 15);
        $modifierLimit = (int) config('sandbox.limits.ai_modifiers', 10);
        $templateLimit = (int) config('sandbox.limits.templates', 10);
        $couponLimit   = (int) config('sandbox.limits.coupons', 5);
        $planLimit     = (int) config('sandbox.limits.plans', 3);
        $faqLimit      = (int) config('sandbox.limits.faqs', 6);
        $userLimit     = (int) config('sandbox.limits.users', 7);

        $expiredPropertyIds = Property::where('id', '>', $propertyLimit)->where('created_at', '<', $cutoff)->pluck('id');
        $expiredThreadIds   = Thread::where('id', '>', $threadLimit)->where('created_at', '<', $cutoff)->pluck('id');
        $expiredCategoryIds = Category::where('id', '>', $categoryLimit)->where('created_at', '<', $cutoff)->pluck('id');

        DB::transaction(function () use (
            $expiredPropertyIds,
            $expiredThreadIds,
            $expiredCategoryIds,
            $matchLimit,
            $imageLimit,
            $messageLimit,
            $clientLimit,
            $contactLimit,
            $modifierLimit,
            $templateLimit,
            $couponLimit,
            $planLimit,
            $faqLimit,
            $userLimit,
            $cutoff
        ) {
            ThreadPropertyMatch::whereIn('thread_id', $expiredThreadIds)
                ->orWhereIn('property_id', $expiredPropertyIds)
                ->orWhere(fn($q) => $q->where('id', '>', $matchLimit)->where('created_at', '<', $cutoff))
                ->delete();

            PropertyImage::whereIn('property_id', $expiredPropertyIds)
                ->orWhere(fn($q) => $q->where('id', '>', $imageLimit)->where('created_at', '<', $cutoff))
                ->delete();

            Message::whereIn('thread_id', $expiredThreadIds)
                ->orWhere(fn($q) => $q->where('id', '>', $messageLimit)->where('created_at', '<', $cutoff))
                ->delete();

            Thread::whereIn('id', $expiredThreadIds)->delete();
            Property::whereIn('id', $expiredPropertyIds)->delete();
            Category::whereIn('id', $expiredCategoryIds)->delete();

            Client::where('id', '>', $clientLimit)->where('created_at', '<', $cutoff)->delete();
            Contact::where('id', '>', $contactLimit)->where('created_at', '<', $cutoff)->delete();
            AiModifier::where('id', '>', $modifierLimit)->where('created_at', '<', $cutoff)->delete();
            Template::where('id', '>', $templateLimit)->where('created_at', '<', $cutoff)->delete();
            Coupon::where('id', '>', $couponLimit)->where('created_at', '<', $cutoff)->delete();
            Plan::where('id', '>', $planLimit)->where('created_at', '<', $cutoff)->delete();
            Faq::where('id', '>', $faqLimit)->where('created_at', '<', $cutoff)->delete();
            User::where('id', '>', $userLimit)->where('created_at', '<', $cutoff)->delete();

            DB::table('notifications')
                ->where(function ($query) use ($userLimit, $cutoff) {
                    $query->where(function ($q) use ($userLimit) {
                        $q->where('notifiable_type', User::class)
                            ->where('notifiable_id', '>', $userLimit);
                    })
                        ->orWhere('created_at', '<', $cutoff);
                })
                ->delete();
        });

        $this->info('   ✅ Expired visitor records & polymorphic notifications purged.');
    }

    /**
     * Stage 3: Self-heal baseline records back to pristine seed definitions
     */
    protected function selfHealBaseline(Carbon $cutoff): void
    {
        $this->comment('3. Self-healing baseline seed records to pristine defaults (respecting 30m grace window)...');

        $modelsToCheck = [
            Property::class   => (int) config('sandbox.limits.properties', 10),
            Category::class   => (int) config('sandbox.limits.categories', 10),
            Client::class     => (int) config('sandbox.limits.clients', 10),
            Faq::class        => (int) config('sandbox.limits.faqs', 6),
            Template::class   => (int) config('sandbox.limits.templates', 10),
            AiModifier::class => (int) config('sandbox.limits.ai_modifiers', 10),
            Plan::class       => (int) config('sandbox.limits.plans', 3),
            Coupon::class     => (int) config('sandbox.limits.coupons', 5),
        ];

        // 1. Snapshot active visitor edits that are STILL inside the 30-minute grace window
        $activeEditsSnapshot = [];

        foreach ($modelsToCheck as $modelClass => $limit) {
            $activeEdits = $modelClass::where('id', '<=', $limit)
                ->where('updated_at', '>=', $cutoff)
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();

            if ($activeEdits->isNotEmpty()) {
                $activeEditsSnapshot[$modelClass] = $activeEdits;
            }
        }

        // 2. Re-seed to heal missing records and revert edits older than 30 minutes
        Model::unguarded(function () {
            $this->callSilent(CategorySeeder::class);
            $this->callSilent(PlanSeeder::class);
            $this->callSilent(CouponSeeder::class);
            $this->callSilent(FaqSeeder::class);
            $this->callSilent(ContactSeeder::class);
            $this->callSilent(ClientSeeder::class);
            $this->callSilent(AiModifierSeeder::class);
            $this->callSilent(TemplateSeeder::class);
            $this->callSilent(PropertySeeder::class);
            $this->callSilent(ThreadSeeder::class);
            $this->callSilent(MessageSeeder::class);
            $this->callSilent(ThreadPropertyMatchSeeder::class);
        });

        // 3. Re-apply active visitor edits that are still within their 30-minute grace window
        $preservedCount = 0;
        foreach ($activeEditsSnapshot as $modelClass => $records) {
            foreach ($records as $record) {
                $originalUpdatedAt = $record->updated_at;

                DB::table((new $modelClass)->getTable())
                    ->where('id', $record->id)
                    ->update([
                        ...$record->getAttributes(),
                        'updated_at' => $originalUpdatedAt,
                    ]);

                $preservedCount++;
            }
        }

        if ($preservedCount > 0) {
            $this->info("   🟢 Preserved {$preservedCount} active baseline edit(s) currently in the 30m grace window.");
        }

        // 4. Synchronize timestamps strictly on PRISTINE records (updated_at = created_at)
        $tablesWithLimits = [
            'properties'   => config('sandbox.limits.properties', 10),
            'categories'   => config('sandbox.limits.categories', 10),
            'clients'      => config('sandbox.limits.clients', 10),
            'threads'      => config('sandbox.limits.threads', 6),
            'templates'    => config('sandbox.limits.templates', 10),
            'plans'        => config('sandbox.limits.plans', 3),
            'coupons'      => config('sandbox.limits.coupons', 5),
            'faqs'         => config('sandbox.limits.faqs', 6),
            'contacts'     => config('sandbox.limits.contacts', 15),
            'ai_modifiers' => config('sandbox.limits.ai_modifiers', 10),
            'users'        => config('sandbox.limits.users', 7),
        ];

        foreach ($tablesWithLimits as $table => $limit) {
            $query = DB::table($table)->where('id', '<=', $limit);

            $activeModel = match ($table) {
                'properties'   => Property::class,
                'categories'   => Category::class,
                'clients'      => Client::class,
                'faqs'         => Faq::class,
                'templates'    => Template::class,
                'ai_modifiers' => AiModifier::class,
                'plans'        => Plan::class,
                'coupons'      => Coupon::class,
                default        => null,
            };

            // Exclude records that are currently in the active grace window
            if ($activeModel && isset($activeEditsSnapshot[$activeModel])) {
                $preservedIds = $activeEditsSnapshot[$activeModel]->pluck('id')->toArray();
                $query->whereNotIn('id', $preservedIds);
            }

            $query->update([
                'updated_at' => DB::raw('created_at'),
            ]);
        }

        $this->info('   ✅ Baseline records restored & timestamps synchronized.');
    }
    /**
     * Stage 4: Purge dead sessions and flush Spatie memory cache
     */
    protected function cleanupSessionsAndCache(): void
    {
        $this->comment('4. Purging stale sessions & flushing permission cache...');

        $sessionTimeout = now()->subMinutes((int) config('sandbox.cleanup_interval_minutes', 30))->timestamp;
        $deletedSessions = DB::table('sessions')->where('last_activity', '<', $sessionTimeout)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info("   ✅ Purged {$deletedSessions} expired sessions. Spatie permission cache flushed.");
    }
}
