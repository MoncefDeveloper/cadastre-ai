<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Property\ListingType;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Models\Category;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(4);

        return [
            'agent_id' => User::factory(),
            'category_id' => Category::factory()->state(['type' => 'property']),
            'listing_type' => $this->faker->randomElement(ListingType::cases()),
            'property_type' => $this->faker->randomElement(PropertyType::cases()),
            'status' => $this->faker->randomElement(PropertyStatus::cases()),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => "### Property Highlights\n\n" . $this->faker->paragraphs(2, true),
            'city' => $this->faker->city(),
            'address' => $this->faker->streetAddress(),
            'price' => $this->faker->numberBetween(15000000, 300000000), // €150,000 to €3,000,000 in cents
            'discount_price' => null,
            'area_sqm' => $this->faker->numberBetween(45, 800),
            'bedrooms' => $this->faker->numberBetween(1, 6),
            'bathrooms' => $this->faker->numberBetween(1, 5),
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}
