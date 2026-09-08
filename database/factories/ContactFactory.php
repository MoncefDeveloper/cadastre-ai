<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ContactMessageStatus;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->optional(0.8)->phoneNumber(),
            'subject' => $this->faker->optional(0.9)->sentence(),
            'message' => $this->faker->paragraph(4),
            'ip_address' => $this->faker->ipv4(),
            'status' => $this->faker->randomElement(ContactMessageStatus::cases()),
        ];
    }
}
