<?php

namespace Database\Factories;

use App\Enums\StatutRecu;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecuFactory extends Factory
{
    protected $model = \App\Models\Recu::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'texte_brut' => $this->faker->text(100),
            'statut' => StatutRecu::en_attente,
            'payload_ia' => null,
            'message_erreur' => null,
        ];
    }

    public function traite(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutRecu::traite,
            'payload_ia' => ['test' => 'data'],
        ]);
    }

    public function echoue(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutRecu::echoue,
            'message_erreur' => 'Erreur lors du traitement',
        ]);
    }
}
