<?php

namespace Tests\Feature;

use App\Models\Depenses;
use App\Models\Recu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DepensesControllerTest extends TestCase
{
    use RefreshDatabase;

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
}
