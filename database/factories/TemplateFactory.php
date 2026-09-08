<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Thread\ThreadChannel;
use App\Models\Category;
use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Template>
 */
class TemplateFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Template>
     */
    protected $model = Template::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Define realistic AI real-estate agent templates
        $archetypes = [
            [
                'name' => 'AI Pitch - Luxury Matching Proposal',
                'channel' => ThreadChannel::EMAIL,
                'system_instructions' => 'You are an elite, highly persuasive French Real Estate agent at MatchMaker. Maintain an elegant, warm, and professional tone. Respond strictly in French. Structure your reply using bullet points for property matches.',
                'prompt' => "Bonjour {{client_name}},\n\nSuite à notre récent échange, j'ai sélectionné pour vous des propriétés d'exception à {{city}} correspondant à vos critères de recherche et votre budget de {{budget_max}}.\n\nVoici les opportunités retenues :\n{{properties_list}}\n\nQuelle date vous conviendrait pour organiser une première visite privée ?\n\nBien cordialement,\n{{agent_name}}\nMatchMaker CRM",
                'variables' => ['client_name', 'city', 'budget_max', 'properties_list', 'agent_name'],
                'rules' => [
                    'tone' => 'elegant',
                    'language' => 'fr',
                    'include_disclaimer' => true,
                    'max_tokens' => 600,
                    'formatting' => 'markdown'
                ],
            ],
            [
                'name' => 'AI WhatsApp - Quick Match Alert',
                'channel' => ThreadChannel::WHATSAPP,
                'system_instructions' => 'You are a friendly, reactive real estate advisor communicating on WhatsApp. Keep responses concise, direct, and under 120 words. Use localized real estate emojis.',
                'prompt' => "Salut {{client_name}} ! 👋 J'ai repéré une pépite qui vient de rentrer sur le marché à {{city}} ! 🏠 Un bien de {{area_sqm}}m² à {{price}} qui colle parfaitement avec tes critères. Dis-moi si tu veux les photos ou une fiche descriptive en priorité ? 😉 - {{agent_name}}",
                'variables' => ['client_name', 'city', 'area_sqm', 'price', 'agent_name'],
                'rules' => [
                    'tone' => 'casual_professional',
                    'max_words' => 120,
                    'emojis' => true,
                    'call_to_action' => 'immediate_reply'
                ],
            ],
            [
                'name' => 'AI Welcome - Lead Auto-Responder',
                'channel' => ThreadChannel::EMAIL,
                'system_instructions' => 'You are an automated CRM routing assistant. Acknowledge new lead inquiries politely, set immediate expectations, and prompt for additional budget parameters.',
                'prompt' => "Bonjour {{client_name}},\n\nNous avons bien reçu votre demande concernant notre annonce pour le bien situé à {{city}}. Un de nos experts dédiés est en train d'examiner votre dossier.\n\nPour affiner notre sélection, pouvez-vous nous confirmer votre budget maximum de {{budget_max}} ?\n\nÀ très vite,\nL'équipe MatchMaker",
                'variables' => ['client_name', 'city', 'budget_max'],
                'rules' => [
                    'auto_reply' => true,
                    'delay_seconds' => 300,
                    'priority_score' => 10
                ],
            ]
        ];

        $archetype = fake()->randomElement($archetypes);

        return [
            // Relationships
            'category_id' => Category::factory()->template(),

            // Data
            'name' => $archetype['name'],
            'channel' => $archetype['channel'],
            'system_instructions' => $archetype['system_instructions'],
            'prompt' => $archetype['prompt'],
            'variables' => $archetype['variables'], // Laravel model automatically serializes to JSON
            'rules' => $archetype['rules'],         // Laravel model automatically serializes to JSON
            'is_active' => true,
        ];
    }

    /**
     * State to configure a template specifically for Email channels.
     */
    public function email(): static
    {
        return $this->state(fn (array $attributes) => [
            'channel' => ThreadChannel::EMAIL,
        ]);
    }

    /**
     * State to configure a template specifically for WhatsApp channels.
     */
    public function whatsapp(): static
    {
        return $this->state(fn (array $attributes) => [
            'channel' => ThreadChannel::WHATSAPP,
        ]);
    }
}
