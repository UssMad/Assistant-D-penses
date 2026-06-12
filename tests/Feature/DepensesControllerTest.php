<?php

namespace Tests\Feature;

use App\Enums\CategorieDepense;
use App\Models\Depenses;
use App\Models\Recu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DepensesControllerTest extends TestCase
{
    use RefreshDatabase;

    // ---- Existing index/show tests ----

    #[Test]
    public function user_can_view_own_expenses_index()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);
        Depenses::factory()->count(3)->create(['recu_id' => $recu->id]);

        $this->actingAs($user)
            ->get(route('depenses.index'))
            ->assertOk();
    }

    #[Test]
    public function user_only_sees_own_expenses()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $recu = Recu::factory()->create(['user_id' => $user->id]);
        $otherRecu = Recu::factory()->create(['user_id' => $other->id]);

        Depenses::factory()->create(['recu_id' => $recu->id]);
        Depenses::factory()->count(2)->create(['recu_id' => $otherRecu->id]);

        $response = $this->actingAs($user)
            ->get(route('depenses.index'));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('depenses'));
    }

    #[Test]
    public function user_can_view_own_expense_detail()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);
        $depense = Depenses::factory()->create(['recu_id' => $recu->id]);

        $this->actingAs($user)
            ->get(route('depenses.show', $depense))
            ->assertOk();
    }

    #[Test]
    public function user_cannot_view_others_expense()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $owner->id]);
        $depense = Depenses::factory()->create(['recu_id' => $recu->id]);

        $this->actingAs($other)
            ->get(route('depenses.show', $depense))
            ->assertNotFound();
    }

    #[Test]
    public function guest_cannot_access_expenses()
    {
        $this->get(route('depenses.index'))->assertRedirect(route('login'));
    }

    // ---- 4.1 Guest redirect for all expense routes ----

    #[Test]
    public function guest_cannot_access_create_expense()
    {
        $this->get(route('depenses.create'))->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_store_expense()
    {
        $this->post(route('depenses.store'))->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_access_edit_expense()
    {
        $depense = Depenses::factory()->create();

        $this->get(route('depenses.edit', $depense))->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_update_expense()
    {
        $depense = Depenses::factory()->create();

        $this->put(route('depenses.update', $depense))->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_destroy_expense()
    {
        $depense = Depenses::factory()->create();

        $this->delete(route('depenses.destroy', $depense))->assertRedirect(route('login'));
    }

    // ---- 4.2 Category filter ----

    #[Test]
    public function expenses_can_be_filtered_by_category()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        Depenses::factory()->create(['recu_id' => $recu->id, 'categorie' => CategorieDepense::alimentaire]);
        Depenses::factory()->create(['recu_id' => $recu->id, 'categorie' => CategorieDepense::alimentaire]);
        Depenses::factory()->create(['recu_id' => $recu->id, 'categorie' => CategorieDepense::boissons]);

        $response = $this->actingAs($user)
            ->get(route('depenses.index', ['categorie' => 'alimentaire']));

        $response->assertOk();
        $this->assertCount(2, $response->viewData('depenses'));
    }

    // ---- 4.3 Create expense ----

    #[Test]
    public function user_can_create_expense()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('depenses.store'), [
                'recu_id' => $recu->id,
                'libelle' => 'Pain',
                'quantite' => 2,
                'prix_unitaire' => 1.50,
                'categorie' => 'alimentaire',
            ])
            ->assertRedirect(route('depenses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('depenses', [
            'recu_id' => $recu->id,
            'libelle' => 'Pain',
        ]);
    }

    #[Test]
    public function user_cannot_create_expense_with_invalid_data()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('depenses.store'), [
                'recu_id' => null,
                'libelle' => '',
                'quantite' => 0,
                'prix_unitaire' => -1,
                'categorie' => '',
            ])
            ->assertSessionHasErrors(['recu_id', 'libelle', 'quantite', 'prix_unitaire', 'categorie']);
    }

    #[Test]
    public function user_cannot_create_expense_for_another_users_receipt()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->post(route('depenses.store'), [
                'recu_id' => $recu->id,
                'libelle' => 'Pain',
                'quantite' => 1,
                'prix_unitaire' => 1.00,
                'categorie' => 'alimentaire',
            ])
            ->assertSessionHasErrors(['recu_id']);
    }

    // ---- 4.4 Edit expense ----

    #[Test]
    public function user_can_edit_own_expense()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);
        $depense = Depenses::factory()->create(['recu_id' => $recu->id]);

        $this->actingAs($user)
            ->put(route('depenses.update', $depense), [
                'libelle' => 'Pain complet',
                'quantite' => 3,
                'prix_unitaire' => 2.00,
                'categorie' => 'alimentaire',
            ])
            ->assertRedirect(route('depenses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('depenses', [
            'id' => $depense->id,
            'libelle' => 'Pain complet',
            'quantite' => 3,
        ]);
    }

    #[Test]
    public function user_cannot_edit_another_users_expense()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $owner->id]);
        $depense = Depenses::factory()->create(['recu_id' => $recu->id]);

        $this->actingAs($other)
            ->put(route('depenses.update', $depense), [
                'libelle' => 'Hijacked',
                'quantite' => 1,
                'prix_unitaire' => 1.00,
                'categorie' => 'autre',
            ])
            ->assertNotFound();
    }

    // ---- 4.5 Delete expense ----

    #[Test]
    public function user_can_delete_own_expense_and_receipt_is_preserved()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);
        $depense1 = Depenses::factory()->create(['recu_id' => $recu->id]);
        $depense2 = Depenses::factory()->create(['recu_id' => $recu->id]);

        $this->actingAs($user)
            ->delete(route('depenses.destroy', $depense1))
            ->assertRedirect();

        $this->assertDatabaseMissing('depenses', ['id' => $depense1->id]);
        $this->assertDatabaseHas('depenses', ['id' => $depense2->id]);
        $this->assertDatabaseHas('recus', ['id' => $recu->id]);
    }

    #[Test]
    public function user_cannot_delete_another_users_expense()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $owner->id]);
        $depense = Depenses::factory()->create(['recu_id' => $recu->id]);

        $this->actingAs($other)
            ->delete(route('depenses.destroy', $depense))
            ->assertNotFound();
    }
}
