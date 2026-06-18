<?php

namespace Tests\Feature;

use App\Enums\StatutRecu;
use App\Jobs\ExtraireDepensesDuRecu;
use App\Models\Depenses;
use App\Models\Recu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecuControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_create_receipt()
    {
        $user = User::factory()->create();

        Queue::fake();

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

        Queue::assertPushed(ExtraireDepensesDuRecu::class);
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

    #[Test]
    public function user_can_view_edit_form()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('recus.edit', $recu))
            ->assertOk()
            ->assertSee('Modifier le Reçu')
            ->assertSee($recu->texte_brut);
    }

    #[Test]
    public function user_cannot_view_others_edit_form()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->get(route('recus.edit', $recu))
            ->assertNotFound();
    }

    #[Test]
    public function user_can_update_own_receipt()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create([
            'user_id' => $user->id,
            'statut' => StatutRecu::traite,
        ]);

        Queue::fake();

        $this->actingAs($user)
            ->put(route('recus.update', $recu), [
                'texte_brut' => 'Updated receipt text with enough characters here',
            ])
            ->assertRedirect(route('recus.show', $recu))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('recus', [
            'id' => $recu->id,
            'texte_brut' => 'Updated receipt text with enough characters here',
            'statut' => 'en_attente',
        ]);

        Queue::assertPushed(ExtraireDepensesDuRecu::class);
    }

    #[Test]
    public function user_cannot_update_receipt_with_short_text()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->put(route('recus.update', $recu), [
                'texte_brut' => 'Short',
            ])
            ->assertSessionHasErrors(['texte_brut']);
    }

    #[Test]
    public function user_cannot_update_others_receipt()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->put(route('recus.update', $recu), [
                'texte_brut' => 'Updated text with enough characters for validation',
            ])
            ->assertNotFound();
    }

    #[Test]
    public function index_filters_by_status()
    {
        $user = User::factory()->create();
        Recu::factory()->create(['user_id' => $user->id, 'statut' => StatutRecu::en_attente]);
        Recu::factory()->create(['user_id' => $user->id, 'statut' => StatutRecu::traite]);
        Recu::factory()->create(['user_id' => $user->id, 'statut' => StatutRecu::echoue]);

        $response = $this->actingAs($user)
            ->get(route('recus.index', ['status' => 'en_attente']))
            ->assertOk();

        $response->assertSee(StatutRecu::en_attente->label());
    }

    #[Test]
    public function index_searches_by_text()
    {
        $user = User::factory()->create();
        Recu::factory()->create(['user_id' => $user->id, 'texte_brut' => 'tomates et oignons']);
        Recu::factory()->create(['user_id' => $user->id, 'texte_brut' => 'lait et pain']);

        $response = $this->actingAs($user)
            ->get(route('recus.index', ['search' => 'tomates']))
            ->assertOk();

        $response->assertSee('tomates');
    }

    #[Test]
    public function index_combines_search_and_filter()
    {
        $user = User::factory()->create();
        Recu::factory()->create([
            'user_id' => $user->id,
            'texte_brut' => 'tomates fraiches',
            'statut' => StatutRecu::en_attente,
        ]);
        Recu::factory()->create([
            'user_id' => $user->id,
            'texte_brut' => 'tomates sechees',
            'statut' => StatutRecu::traite,
        ]);

        $response = $this->actingAs($user)
            ->get(route('recus.index', ['search' => 'tomates', 'status' => 'en_attente']))
            ->assertOk();

        $response->assertSee('En attente');
    }

    #[Test]
    public function user_can_retry_failed_receipt()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create([
            'user_id' => $user->id,
            'statut' => StatutRecu::echoue,
        ]);
        $depense = Depenses::factory()->create(['recu_id' => $recu->id]);

        Queue::fake();

        $this->actingAs($user)
            ->post(route('recus.retry', $recu))
            ->assertRedirect(route('recus.show', $recu))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('recus', [
            'id' => $recu->id,
            'statut' => 'en_attente',
            'payload_ia' => null,
            'message_erreur' => null,
        ]);

        $this->assertModelMissing($depense);

        Queue::assertPushed(ExtraireDepensesDuRecu::class);
    }

    #[Test]
    public function user_cannot_retry_non_failed_receipt()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create([
            'user_id' => $user->id,
            'statut' => StatutRecu::en_attente,
        ]);

        $this->actingAs($user)
            ->post(route('recus.retry', $recu))
            ->assertSessionHas('error');
    }

    #[Test]
    public function user_cannot_retry_others_receipt()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create([
            'user_id' => $owner->id,
            'statut' => StatutRecu::echoue,
        ]);

        $this->actingAs($other)
            ->post(route('recus.retry', $recu))
            ->assertNotFound();
    }

    #[Test]
    public function show_displays_total_for_processed_receipt()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create([
            'user_id' => $user->id,
            'statut' => StatutRecu::traite,
        ]);
        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'quantite' => 2,
            'prix_unitaire' => 10.50,
        ]);
        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'quantite' => 3,
            'prix_unitaire' => 5.00,
        ]);

        $this->actingAs($user)
            ->get(route('recus.show', $recu))
            ->assertOk()
            ->assertSee('36.00'); // 2*10.50 + 3*5.00 = 21.00 + 15.00 = 36.00
    }
}
