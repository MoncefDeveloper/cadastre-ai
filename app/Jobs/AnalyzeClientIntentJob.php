<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\Property\ListingType;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Models\Message;
use App\Models\Property;
use App\Models\ThreadPropertyMatch;
use App\Services\AI\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyzeClientIntentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public bool $deleteWhenMissingModels = true;

    // Gracefully handle Gemini's 15 RPM limit
    public int $tries = 3;
    public array $backoff = [10, 30, 60];
    public int $timeout = 45;

    public function __construct(
        public readonly Message $message
    ) {}

    public function handle(GeminiService $gemini): void
    {
        $thread = $this->message->thread;
        $criteria = $gemini->extractCriteria($this->message);

        // 1. Check Intent: Ignore non-inquiry emails (e.g., "Thanks for the info!")
        if (!isset($criteria['is_property_inquiry']) || $criteria['is_property_inquiry'] === false) {
            Log::info('[AI Intent] Not a property inquiry. Skipping matching.', ['thread_id' => $thread->id]);
            return;
        }

        // 2. Map AI Strings to native PHP Enums
        $listingType = ListingType::fromString($criteria['listing_type'] ?? null);
        $propertyType = PropertyType::fromString($criteria['property_type'] ?? null);
        $city = $criteria['city'] ?? null;

        // Convert extracted budget to cents (assuming database stores in cents)
        $budgetMaxCents = isset($criteria['budget_max']) ? (int) ($criteria['budget_max'] * 100) : null;

        // 3. Save memory context to the Thread
        $thread->update(['extracted_criteria' => $criteria]);

        // 4. Execute the Tiered Property Match Strategy
        $properties = $this->findMatchingProperties(
            city: $city,
            listingType: $listingType,
            propertyType: $propertyType,
            budgetMax: $budgetMaxCents,
            beds: $criteria['bedrooms'] ?? null,
            baths: $criteria['bathrooms'] ?? null
        );

        if ($properties->isEmpty()) {
            Log::warning('[AI Intent] No properties found even with fallback.', ['thread_id' => $thread->id]);
            // Still dispatch response job; AI will apologize for no inventory.
            DraftAiResponseJob::dispatch($thread);
            return;
        }

        // 5. Save Matches to Pivot Table
        DB::transaction(function () use ($thread, $properties) {
            // Optional: Clear previous non-rejected matches to refresh the UI
            ThreadPropertyMatch::where('thread_id', $thread->id)->where('is_rejected', false)->delete();

            foreach ($properties as $property) {
                ThreadPropertyMatch::create([
                    'thread_id' => $thread->id,
                    'property_id' => $property->id,
                    'match_score' => 95.00, // Fixed for now, can be calculated dynamically later
                ]);
            }
        });

        // 6. Dispatch the Drafting Job
        Log::info('[AI Intent] Matches found, drafting response.', ['thread_id' => $thread->id]);
        DraftAiResponseJob::dispatch($thread);
    }

    /**
     * Executes the strict fallback real estate logic.
     */
    private function findMatchingProperties(
        ?string $city,
        ?ListingType $listingType,
        ?PropertyType $propertyType,
        ?int $budgetMax,
        ?int $beds,
        ?int $baths
    ) {
        $baseQuery = Property::where('status', PropertyStatus::AVAILABLE);

        if ($city) $baseQuery->where('city', 'like', "%{$city}%");
        if ($listingType) $baseQuery->where('listing_type', $listingType);

        // Attempt 1: Strict Match
        $query1 = (clone $baseQuery);
        if ($propertyType) $query1->where('property_type', $propertyType);
        if ($budgetMax) $query1->where('price', '<=', $budgetMax);
        if ($beds) $query1->where('bedrooms', '>=', $beds);
        if ($baths) $query1->where('bathrooms', '>=', $baths);

        $results = $query1->orderByDesc('is_featured')->latest()->limit(3)->get();
        if ($results->isNotEmpty()) return $results;

        // Attempt 2: Drop Beds & Baths
        $query2 = (clone $baseQuery);
        if ($propertyType) $query2->where('property_type', $propertyType);
        if ($budgetMax) $query2->where('price', '<=', $budgetMax);

        $results = $query2->orderByDesc('is_featured')->latest()->limit(3)->get();
        if ($results->isNotEmpty()) return $results;

        // Attempt 3: The Real Estate Fix - KEEP City, DROP Property Type, BUMP Budget by 15%
        $query3 = (clone $baseQuery);
        if ($budgetMax) {
            $bumpedBudget = (int) ($budgetMax * 1.15);
            $query3->where('price', '<=', $bumpedBudget);
        }

        return $query3->orderByDesc('is_featured')->latest()->limit(3)->get();
    }
}
