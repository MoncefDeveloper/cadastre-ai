<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiModifier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiModifier>
 */
class AiModifierFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<AiModifier>
     */
    protected $model = AiModifier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Creative Real-Estate focused modifiers to reshape AI drafts
        $modifiers = [
            [
                'label' => 'Adoucir le ton',
                'instruction' => 'Reformule ce message en adoptant un ton chaleureux, rassurant et très empathique. Parfait pour un client qui hésite ou qui a des craintes.',
                'color' => 'success',
            ],
            [
                'label' => 'Style Ultra-Luxe',
                'instruction' => 'Réécris ce texte en utilisant un vocabulaire immobilier de prestige (ex: "demeure d\'exception", "matériaux nobles"). Adopte un ton élégant, feutré et exclusif.',
                'color' => 'primary',
            ],
            [
                'label' => 'Créer de l\'urgence',
                'instruction' => 'Ajoute subtilement un sentiment d\'opportunité unique et d\'urgence (ex: "forte demande sur ce secteur", "visites prévues cette semaine"). Incite à répondre rapidement.',
                'color' => 'warning',
            ],
            [
                'label' => 'Focus Rentabilité (Investisseur)',
                'instruction' => 'Oriente le message en insistant sur les indicateurs financiers : potentiel de valorisation à terme, attractivité locative du quartier et optimisation fiscale.',
                'color' => 'info',
            ],
            [
                'label' => 'Synthétiser pour SMS/WhatsApp',
                'instruction' => 'Raccourcis drastiquement le message sous forme de points clés percutants (moins de 100 mots). Ajoute des émojis pertinents (🏠, 🔑, ⚡) et aère le texte.',
                'color' => 'gray',
            ],
            [
                'label' => 'Négociation délicate',
                'instruction' => 'Formule les termes financiers ou les refus d\'offre avec une diplomatie extrême. Valorise la relation de confiance tout en restant ferme sur les positions du vendeur.',
                'color' => 'danger',
            ],
        ];

        $modifier = fake()->randomElement($modifiers);

        return [
            // Relationships: Null means it is a Global modifier.
            // We can chain states to link specific modifiers to custom agents.
            'user_id' => null,

            // Config Data
            'label' => $modifier['label'],
            'instruction' => $modifier['instruction'],
            'color' => $modifier['color'],
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
        ];
    }

    /**
     * State to configure a global modifier available to all agents.
     */
    public function global(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }

    /**
     * State to bind a modifier to a specific agent's customized shortcuts.
     */
    public function personal(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
