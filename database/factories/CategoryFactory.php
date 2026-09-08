<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Category\CategoryType;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Category>
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'description' => fake()->optional(0.8)->sentence(),
            'icon' => fake()->randomElement(['home', 'document-text', 'tag', 'user', 'briefcase', 'chat-bubble-left']),
            'color' => fake()->randomElement(['primary', 'success', 'warning', 'danger', 'info', 'gray']),
            'is_active' => true,
            'type' => fake()->randomElement(CategoryType::cases()),
            'parent_id' => null,
        ];
    }

    /**
     * State to explicitly set the type to PROPERTY.
     */
    public function property(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CategoryType::PROPERTY,
        ]);
    }

    /**
     * State to explicitly set the type to TEMPLATE.
     */
    public function template(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CategoryType::TEMPLATE,
        ]);
    }

    /**
     * State to explicitly set the type to CLIENT_TAG.
     */
    public function clientTag(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CategoryType::CLIENT_TAG,
        ]);
    }

    /**
     * State to create a child category under a specific parent.
     */
    public function childOf(Category $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'type' => $parent->type, // Keep child type synchronized with parent
        ]);
    }
}
