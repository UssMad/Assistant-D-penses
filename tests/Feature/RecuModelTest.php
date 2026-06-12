<?php

namespace Tests\Feature;

use App\Enums\StatutRecu;
use App\Models\Depenses;
use App\Models\Recu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecuModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_create_recu()
    {
        $user = User::factory()->create();

        $recu = Recu::factory()->create([
            'user_id' => $user->id,
            'texte_brut' => 'Test receipt text',
        ]);

        $this->assertDatabaseHas('recus', [
            'id' => $recu->id,
            'user_id' => $user->id,
            'texte_brut' => 'Test receipt text',
        ]);
    }

    #[Test]
    public function recu_defaults_to_en_attente()
    {
        $recu = Recu::factory()->create();

        $this->assertEquals(StatutRecu::en_attente, $recu->statut);
    }

    #[Test]
    public function recu_belongs_to_user()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $recu->user);
        $this->assertTrue($recu->user->is($user));
    }

    #[Test]
    public function recu_has_many_depenses()
    {
        $recu = Recu::factory()->create();
        $depense = Depenses::factory()->create(['recu_id' => $recu->id]);

        $this->assertTrue($recu->depenses->contains($depense));
    }

    #[Test]
    public function deleting_recu_cascades_to_depenses()
    {
        $recu = Recu::factory()->create();
        $depense = Depenses::factory()->create(['recu_id' => $recu->id]);

        $recu->delete();

        $this->assertModelMissing($recu);
        $this->assertModelMissing($depense);
    }

    #[Test]
    public function recu_statut_can_change_to_traite()
    {
        $recu = Recu::factory()->create();

        $recu->update([
            'statut' => StatutRecu::traite,
            'payload_ia' => ['articles' => []],
        ]);

        $this->assertEquals(StatutRecu::traite, $recu->fresh()->statut);
        $this->assertEquals(['articles' => []], $recu->fresh()->payload_ia);
    }

    #[Test]
    public function recu_statut_can_change_to_echoue()
    {
        $recu = Recu::factory()->create();

        $recu->update([
            'statut' => StatutRecu::echoue,
            'message_erreur' => 'Erreur de connexion API',
        ]);

        $this->assertEquals(StatutRecu::echoue, $recu->fresh()->statut);
        $this->assertEquals('Erreur de connexion API', $recu->fresh()->message_erreur);
    }
}
