<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Property\ListingType;
use App\Enums\Property\PropertyType;
use App\Enums\Thread\ThreadChannel;
use App\Enums\Thread\ThreadPriority;
use App\Enums\Thread\ThreadStatus;
use App\Models\Client;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Thread>
 */
class ThreadFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Thread>
     */
    protected $model =  Thread::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = fake()->randomElement(['Paris', 'Lyon', 'Marseille', 'Bordeaux', 'Nice']);
        $propertyType = fake()->randomElement([PropertyType::APARTMENT, PropertyType::VILLA, PropertyType::STUDIO]);
        $listingType = fake()->randomElement(ListingType::cases());
        $status = fake()->randomElement(ThreadStatus::cases());

        $subject = $this->generateRealisticSubject($city, $propertyType, $listingType);
        $criteria = $this->generateExtractedCriteria($city, $propertyType, $listingType);

        return [
            // Relationships
            'client_id' => Client::factory(),
            'assigned_user_id' => User::factory(),

            // External Routing Metrics
            'mailbox_hash' => fake()->unique()->regexify('[a-f0-9]{16}'),

            // Thread Information
            'subject' => $subject,
            'status' => $status,
            'priority' => fake()->randomElement(ThreadPriority::cases()),
            'channel' => fake()->randomElement(ThreadChannel::cases()),
            'is_unread' => fake()->boolean(40), // 40% chance of being unread

            // Conditional snoozing logic
            'snoozed_until' => $status === ThreadStatus::SNOOZED
                ? now()->addDays(fake()->numberBetween(1, 7))
                : null,

            // AI Memory Block
            'extracted_criteria' => $criteria, // Laravel handles serialization

            // Metrics Caching
            'last_message_at' => now()->subMinutes(fake()->numberBetween(10, 10080)), // Last 7 days
        ];
    }

    /**
     * Generate localized inquiry subject lines.
     */
    private function generateRealisticSubject(string $city, PropertyType $propertyType, ListingType $listingType): string
    {
        $action = $listingType === ListingType::SALE ? 'Achat' : 'Location';

        $descriptor = match ($propertyType) {
            PropertyType::STUDIO => 'Studio équipé / meublé',
            PropertyType::VILLA => 'Maison avec extérieur',
            default => 'Appartement familial', // APARTMENT
        };

        return fake()->randomElement([
            "Demande d'informations - {$descriptor} à {$city}",
            "Projet d'{$action} - {$city} ({$descriptor})",
            "Nouveau message de recherche : {$descriptor} ({$city})",
            "Intérêt pour annonce immobilière sur {$city}",
        ]);
    }

    /**
     * Generate highly structured, localized real-estate search criteria (financials in CENTS).
     */
    private function generateExtractedCriteria(string $city, PropertyType $propertyType, ListingType $listingType): array
    {
        // Set realistic max budgets depending on market value of chosen city (in Cents)
        $budgetMaxInCents = match ($listingType) {
            ListingType::RENT => match ($city) {
                'Paris' => fake()->numberBetween(1_200_00, 3_500_00), // €1,200 to €3,500/mo
                default => fake()->numberBetween(600_00, 1_800_00),   // €600 to €1,800/mo
            },
            ListingType::SALE => match ($city) {
                'Paris' => fake()->numberBetween(550_000_00, 2_500_000_00), // €550,000 to €2,500,000
                'Nice', 'Lyon' => fake()->numberBetween(350_000_00, 1_200_000_00),
                default => fake()->numberBetween(200_000_00, 800_000_00), // Bordeaux, Marseille
            }
        };

        $minArea = match ($propertyType) {
            PropertyType::STUDIO => fake()->numberBetween(15, 25),
            PropertyType::VILLA => fake()->numberBetween(100, 180),
            default => fake()->numberBetween(40, 90),
        };

        return [
            'city' => $city,
            'budget_max' => $budgetMaxInCents,
            'property_type' => $propertyType->value, // Cast to raw value for JSON compatibility
            'listing_type' => $listingType->value,   // Cast to raw value for JSON compatibility
            'min_area_sqm' => $minArea,
            'min_bedrooms' => $propertyType === PropertyType::STUDIO ? 0 : fake()->numberBetween(1, 3),
            'parking_required' => fake()->boolean(25),
        ];
    }

    /**
     * Explicitly set the thread status to SNOOZED and configure an expiration date.
     */
    public function snoozed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ThreadStatus::SNOOZED,
            'snoozed_until' => now()->addDays(fake()->numberBetween(1, 7)),
        ]);
    }

    /**
     * Explicitly set the thread status to OPEN.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ThreadStatus::OPEN,
            'snoozed_until' => null,
        ]);
    }

    /**
     * Explicitly set the thread status to CLOSED.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ThreadStatus::CLOSED,
            'snoozed_until' => null,
        ]);
    }
}
