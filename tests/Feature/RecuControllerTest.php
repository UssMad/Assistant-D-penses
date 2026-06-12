<?php

namespace Tests\Feature;

use App\Models\Depenses;
use App\Models\Recu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecuControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_create_receipt()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('recus.store'), [
                'texte_brut' => 'Valid receipt text with enough characters',
            ])
            ->assertRedirect(route('recus.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('recus', [
            'user_id' => $user->id,
            'statut' => 'en_attente',
        ]);
    }

    #[Test]
    public function user_cannot_create_receipt_with_short_text()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('recus.store'), [
                'texte_brut' => 'Short',
            ])
            ->assertSessionHasErrors(['texte_brut']);
    }

    #[Test]
    public function user_can_view_own_receipt()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('recus.show', $recu))
            ->assertOk();
    }

    #[Test]
    public function user_cannot_view_others_receipt()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->get(route('recus.show', $recu))
            ->assertNotFound();
    }

    #[Test]
    public function user_can_delete_own_receipt()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('recus.destroy', $recu))
            ->assertRedirect(route('recus.index'));

        $this->assertModelMissing($recu);
    }

    #[Test]
    public function user_cannot_delete_others_receipt()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->delete(route('recus.destroy', $recu))
            ->assertNotFound();

        $this->assertModelExists($recu);
    }

    #[Test]
    public function deleting_receipt_cascades_to_expenses()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);
        $depense = Depenses::factory()->create(['recu_id' => $recu->id]);

        $this->actingAs($user)
            ->delete(route('recus.destroy', $recu));

        $this->assertModelMissing($recu);
        $this->assertModelMissing($depense);
    }

    #[Test]
    public function user_sees_only_own_receipts_on_index()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Recu::factory()->count(3)->create(['user_id' => $user->id]);
        Recu::factory()->count(2)->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->get(route('recus.index'))
            ->assertOk()
            ->assertSee('Nouveau Reçu');
    }
}
