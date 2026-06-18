<?php

namespace Database\Factories;

use App\Enums\CategorieDepense;
use App\Models\Recu;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepensesFactory extends Factory
{
    protected $model = \App\Models\Depenses::class;

    public function definition(): array
    {
        return [
            'recu_id' => Recu::factory(),
            'libelle' => $this->faker->word(),
            'quantite' => $this->faker->numberBetween(1, 10),
            'prix_unitaire' => $this->faker->randomFloat(2, 1, 100),
            'categorie' => $this->faker->randomElement(CategorieDepense::cases()),
        ];
    }
}
