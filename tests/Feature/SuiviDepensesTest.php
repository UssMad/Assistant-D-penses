<?php

namespace Tests\Feature;

use App\Enums\CategorieDepense;
use App\Models\Depenses;
use App\Models\Recu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuiviDepensesTest extends TestCase
{
    use RefreshDatabase;

    // ---- Dashboard ----

    #[Test]
    public function dashboard_renders_for_authenticated_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    #[Test]
    public function dashboard_is_blocked_for_guest()
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function dashboard_shows_correct_monthly_total()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'quantite' => 2,
            'prix_unitaire' => 10.00,
            'created_at' => now(),
        ]);

        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'quantite' => 3,
            'prix_unitaire' => 5.00,
            'created_at' => now()->subMonths(2),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('20.00');
    }

    #[Test]
    public function dashboard_shows_pending_receipts_count()
    {
        $user = User::factory()->create();
        Recu::factory()->create(['user_id' => $user->id, 'statut' => 'en_attente']);
        Recu::factory()->create(['user_id' => $user->id, 'statut' => 'en_attente']);
        Recu::factory()->create(['user_id' => $user->id, 'statut' => 'traite']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('2');
    }

    // ---- Date filters ----

    #[Test]
    public function expense_index_filters_by_date_from()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $old = Depenses::factory()->create([
            'recu_id' => $recu->id,
            'created_at' => now()->subMonths(3),
        ]);
        $recent = Depenses::factory()->create([
            'recu_id' => $recu->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('depenses.index', ['date_from' => now()->subMonth()->format('Y-m-d')]))
            ->assertOk();

        $response->assertSee($recent->libelle);
    }

    #[Test]
    public function expense_index_filters_by_date_to()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $old = Depenses::factory()->create([
            'recu_id' => $recu->id,
            'created_at' => now()->subMonths(3),
        ]);
        $recent = Depenses::factory()->create([
            'recu_id' => $recu->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('depenses.index', ['date_to' => now()->subMonth()->format('Y-m-d')]))
            ->assertOk();

        $response->assertSee($old->libelle);
    }

    #[Test]
    public function expense_index_combines_category_and_date_filters()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'categorie' => CategorieDepense::alimentaire,
            'created_at' => now(),
        ]);
        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'categorie' => CategorieDepense::boissons,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('depenses.index', [
                'categorie' => 'alimentaire',
                'date_from' => now()->subDays(1)->format('Y-m-d'),
                'date_to' => now()->addDays(1)->format('Y-m-d'),
            ]))
            ->assertOk();

        $response->assertSee(CategorieDepense::alimentaire->label());
    }

    // ---- Summary ----

    #[Test]
    public function summary_shows_correct_totals()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'categorie' => CategorieDepense::alimentaire,
            'quantite' => 2,
            'prix_unitaire' => 10.00,
        ]);
        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'categorie' => CategorieDepense::boissons,
            'quantite' => 3,
            'prix_unitaire' => 5.00,
        ]);

        $this->actingAs($user)
            ->get(route('depenses.summary'))
            ->assertOk()
            ->assertSee('Alimentaire')
            ->assertSee('Boissons')
            ->assertSee('35.00');
    }

    #[Test]
    public function summary_filters_by_date_range()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'categorie' => CategorieDepense::alimentaire,
            'created_at' => now()->subMonths(3),
        ]);
        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'categorie' => CategorieDepense::boissons,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('depenses.summary', [
                'date_from' => now()->subMonth()->format('Y-m-d'),
            ]))
            ->assertOk();

        $response->assertSee('Boissons');
    }

    #[Test]
    public function summary_shows_empty_state()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('depenses.summary'))
            ->assertOk()
            ->assertSee('Aucune dépense pour cette période.');
    }

    #[Test]
    public function summary_enforces_data_isolation()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $otherRecu = Recu::factory()->create(['user_id' => $other->id]);
        Depenses::factory()->create([
            'recu_id' => $otherRecu->id,
            'categorie' => CategorieDepense::alimentaire,
        ]);

        $this->actingAs($user)
            ->get(route('depenses.summary'))
            ->assertOk()
            ->assertSee('Aucune dépense pour cette période.');
    }

    // ---- Export ----

    #[Test]
    public function export_returns_csv_with_headers()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);
        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'libelle' => 'Tomates',
            'quantite' => 5,
            'prix_unitaire' => 3.00,
            'categorie' => CategorieDepense::alimentaire,
        ]);

        $response = $this->actingAs($user)
            ->get(route('depenses.export'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8');

        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    #[Test]
    public function export_respects_category_filter()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'libelle' => 'Eau',
            'categorie' => CategorieDepense::boissons,
        ]);
        Depenses::factory()->create([
            'recu_id' => $recu->id,
            'libelle' => 'Savon',
            'categorie' => CategorieDepense::hygiene,
        ]);

        $response = $this->actingAs($user)
            ->get(route('depenses.export', ['categorie' => 'boissons']))
            ->assertOk();

        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    #[Test]
    public function export_is_blocked_for_guests()
    {
        $this->get(route('depenses.export'))
            ->assertRedirect(route('login'));
    }
}
