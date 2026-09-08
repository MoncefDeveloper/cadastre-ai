<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence() . '?',
            'answer' => '<p>' . $this->faker->paragraph(3) . '</p>',
            'target_audience' => $this->faker->randomElement(['global', 'agents', 'clients', 'billing']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
