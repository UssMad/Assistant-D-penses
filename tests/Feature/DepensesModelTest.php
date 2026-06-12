<?php

namespace Tests\Feature;

use App\Enums\CategorieDepense;
use App\Models\Depenses;
use App\Models\Recu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DepensesModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_create_depense()
    {
        $recu = Recu::factory()->create();

        $depense = Depenses::factory()->create([
            'recu_id' => $recu->id,
            'libelle' => 'Pain',
            'quantite' => 2,
            'prix_unitaire' => 1.50,
            'categorie' => CategorieDepense::alimentaire,
        ]);

        $this->assertDatabaseHas('depenses', [
            'id' => $depense->id,
            'recu_id' => $recu->id,
            'libelle' => 'Pain',
            'quantite' => 2,
            'prix_unitaire' => 1.50,
        ]);
    }

    #[Test]
    public function depense_belongs_to_recu()
    {
        $recu = Recu::factory()->create();
        $depense = Depenses::factory()->create(['recu_id' => $recu->id]);

        $this->assertInstanceOf(Recu::class, $depense->recu);
        $this->assertTrue($depense->recu->is($recu));
    }

    #[Test]
    public function depense_casts_categorie_to_enum()
    {
        $depense = Depenses::factory()->create();

        $this->assertInstanceOf(CategorieDepense::class, $depense->categorie);
    }
}
