<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ClientStatus;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Client>
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        // Generate a high-fidelity email match representing enterprise contacts
        $uniqueEmail = Str::lower(sprintf(
            '%s.%s.%d@%s',
            Str::slug($firstName),
            Str::slug($lastName),
            fake()->unique()->numberBetween(10, 9999),
            fake()->safeEmailDomain()
        ));

        // French mobile number format typically starts with +33 6 or +33 7
        $frenchMobilePrefix = fake()->randomElement(['+33 6', '+33 7']);
        $frenchPhone = $frenchMobilePrefix . fake()->numerify(' ## ## ## ##');

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $uniqueEmail,
            'phone' => fake()->optional(0.9)->passthrough($frenchPhone),
            'status' => fake()->randomElement(ClientStatus::cases()),
            'source' => fake()->randomElement([
                'Mailgun Webhook',
                'Manual Input',
                'Website Form',
                'WhatsApp Inbound',
                'SeLoger Portal',
                'Bien' . "'" . 'Ici Portal',
            ]),
        ];
    }

    /**
     * State to explicitly set the status to NEW.
     */
    public function asNew(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ClientStatus::NEW,
        ]);
    }

    /**
     * State to explicitly set the status to ACTIVE.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ClientStatus::ACTIVE,
        ]);
    }

    /**
     * State to explicitly set the status to CLOSED.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ClientStatus::CLOSED,
        ]);
    }
}
