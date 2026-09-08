<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Message\MessageDirection;
use App\Models\Client;
use App\Models\Message;
use App\Models\Template;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Message>
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $direction = fake()->randomElement(MessageDirection::cases());

        $content = $this->generateRealisticContent($direction);

        return [
            // Relationships: default linkages
            'thread_id' => Thread::factory(),

            // Evaluated actor states based on direction
            'user_id' => $direction === MessageDirection::OUTBOUND ? User::factory() : null,
            'client_id' => $direction === MessageDirection::INBOUND ? Client::factory() : null,
            'template_id' => null,

            // Email Standards
            'mailbox_message_id' => fn (array $attributes) => '<' . Str::random(10) . '_' . fake()->unique()->regexify('[a-f0-9]{12}') . '@matchmaker.immo>',
            'in_reply_to' => null,

            // Attributes
            'direction' => $direction,
            'body_text' => $content['text'],
            'body_html' => $content['html'],

            // AI states
            'is_draft' => false,
            'is_ai_generated' => $direction === MessageDirection::OUTBOUND && fake()->boolean(40), // 40% outbound are AI drafts initially

            // Cloud attachments simulation
            'attachments' => fake()->optional(0.1)->passthrough([
                [
                    'name' => 'fiche_descriptive.pdf',
                    'url' => 'https://matchmaker-storage.s3.eu-west-3.amazonaws.com/samples/fiche_descriptive.pdf',
                    'size' => '2.4MB'
                ]
            ]),
        ];
    }

    /**
     * Generate coordinated, localized conversation pairs.
     */
    private function generateRealisticContent(MessageDirection $direction): array
    {
        return match ($direction) {
            MessageDirection::INBOUND => fake()->randomElement([
                [
                    'text' => "Bonjour,\n\nJe serais intéressé pour visiter cet appartement situé à Paris ou Lyon. Est-il disponible la semaine prochaine ?\n\nMerci,\nClient",
                    'html' => "<p>Bonjour,</p><p>Je serais intéressé pour visiter cet appartement situé à <strong>Paris</strong> ou <strong>Lyon</strong>. Est-il disponible la semaine prochaine ?</p><p>Merci,<br>Client</p>"
                ],
                [
                    'text' => "Bonjour,\n\nQuel est le montant approximatif des charges de copropriété mensuelles pour ce bien ? Est-ce qu'un garage est inclus ?\n\nCordialement.",
                    'html' => "<p>Bonjour,</p><p>Quel est le montant approximatif des charges de copropriété mensuelles pour ce bien ? Est-ce qu'un garage est inclus ?</p><p>Cordialement.</p>"
                ],
                [
                    'text' => "Hello ! Je cherche un studio meublé sympa proche des transports sur Nice. Mon budget est serré mais je peux emménager immédiatement. Qu'avez-vous en stock ?",
                    'html' => "<p>Hello ! Je cherche un <strong>studio meublé</strong> sympa proche des transports sur <strong>Nice</strong>. Mon budget est serré mais je peux emménager immédiatement. Qu'avez-vous en stock ?</p>"
                ],
            ]),
            MessageDirection::OUTBOUND => fake()->randomElement([
                [
                    'text' => "Bonjour,\n\nMerci pour votre intérêt. Le bien est en effet disponible pour visites dès jeudi prochain. Seriez-vous libre dans la matinée ?\n\nBien cordialement,\nL'équipe MatchMaker",
                    'html' => "<p>Bonjour,</p><p>Merci pour votre intérêt. Le bien est en effet disponible pour visites dès <strong>jeudi prochain</strong>. Seriez-vous libre dans la matinée ?</p><p>Bien cordialement,<br><strong>L'équipe MatchMaker</strong></p>"
                ],
                [
                    'text' => "Bonjour,\n\nJe vous confirme que les charges de copropriété s'élèvent à environ 140€ par trimestre, incluant l'entretien des parties communes et l'eau froide.\n\nBonne journée.",
                    'html' => "<p>Bonjour,</p><p>Je vous confirme que les charges de copropriété s'élèvent à environ <strong>140€ par trimestre</strong>, incluant l'entretien des parties communes et l'eau froide.</p><p>Bonne journée.</p>"
                ],
            ]),
            MessageDirection::SYSTEM => fake()->randomElement([
                [
                    'text' => "Critères de recherche extraits : {city: Paris, budget_max: 75000000}",
                    'html' => "<span style='color: gray;'>[SYSTEM] Critères de recherche extraits : <strong>{city: Paris, budget_max: 75000000}</strong></span>"
                ],
                [
                    'text' => "Conversation assignée automatiquement à l'agent de permanence.",
                    'html' => "<span style='color: gray;'>[SYSTEM] Conversation assignée automatiquement à l'agent de permanence.</span>"
                ],
            ]),
        };
    }

    /**
     * Configure message specifically as an Inbound reply.
     */
    public function inbound(): static
    {
        return $this->state(fn (array $attributes) => [
            'direction' => MessageDirection::INBOUND,
            'user_id' => null,
            'client_id' => Client::factory(),
        ]);
    }

    /**
     * Configure message specifically as an Outbound reply.
     */
    public function outbound(): static
    {
        return $this->state(fn (array $attributes) => [
            'direction' => MessageDirection::OUTBOUND,
            'client_id' => null,
            'user_id' => User::factory(),
        ]);
    }

    /**
     * Configure message specifically as a pending, AI-generated draft.
     */
    public function pendingAiDraft(?Template $template = null): static
    {
        $draftText = "Bonjour,\n\nEn analysant vos critères pour {{city}}, j'ai identifié deux biens exclusifs qui pourraient retenir votre attention.\n\nSouhaitez-vous recevoir les visites virtuelles ?\n\n[Généré par l'IA MatchMaker]";
        $draftHtml = "<p>Bonjour,</p><p>En analysant vos critères pour <strong>{{city}}</strong>, j'ai identifié deux biens exclusifs qui pourraient retenir votre attention.</p><p>Souhaitez-vous recevoir les visites virtuelles ?</p><p><em>[Généré automatiquement par l'IA MatchMaker]</em></p>";

        return $this->state(fn (array $attributes) => [
            'direction' => MessageDirection::OUTBOUND,
            'client_id' => null,
            'user_id' => null, // Drafts belong to the system queue before agent claims/sends them
            'template_id' => $template?->id,
            'is_draft' => true,
            'is_ai_generated' => true,
            'body_text' => $draftText,
            'body_html' => $draftHtml,
        ]);
    }
}
