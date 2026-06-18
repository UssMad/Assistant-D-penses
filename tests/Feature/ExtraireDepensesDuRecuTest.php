<?php

namespace Tests\Feature;

use App\Ai\ExtractionAgent;
use App\Jobs\ExtraireDepensesDuRecu;
use App\Models\Depenses;
use App\Models\Recu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExtraireDepensesDuRecuTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function job_is_dispatched_after_receipt_creation()
    {
        $user = User::factory()->create();

        Queue::fake();

        $this->actingAs($user)
            ->post(route('recus.store'), [
                'texte_brut' => 'Valid receipt text with enough characters',
            ])
            ->assertRedirect(route('recus.index'));

        Queue::assertPushed(ExtraireDepensesDuRecu::class, function ($job) {
            return is_int($job->recuId);
        });
    }

    #[Test]
    public function successful_extraction_creates_expenses()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create([
            'user_id' => $user->id,
            'texte_brut' => '3x Pain 5dh, 2x Lait 12dh',
            'statut' => 'en_attente',
        ]);

        ExtractionAgent::fake([[
            'articles' => [
                ['libelle' => 'Pain', 'quantite' => 3, 'prix_unitaire' => 5.0, 'categorie' => 'alimentaire'],
                ['libelle' => 'Lait', 'quantite' => 2, 'prix_unitaire' => 12.0, 'categorie' => 'boissons'],
            ],
            'total_estime' => 39.0,
            'devise' => 'MAD',
        ]]);

        (new ExtraireDepensesDuRecu($recu->id))->handle();

        $this->assertDatabaseHas('recus', [
            'id' => $recu->id,
            'statut' => 'traite',
        ]);

        $this->assertDatabaseHas('depenses', [
            'recu_id' => $recu->id,
            'libelle' => 'Pain',
            'quantite' => 3,
            'prix_unitaire' => 5.0,
            'categorie' => 'alimentaire',
        ]);

        $this->assertDatabaseHas('depenses', [
            'recu_id' => $recu->id,
            'libelle' => 'Lait',
            'quantite' => 2,
            'prix_unitaire' => 12.0,
            'categorie' => 'boissons',
        ]);

        $this->assertDatabaseCount('depenses', 2);
    }

    #[Test]
    public function failed_extraction_updates_receipt_to_echoue()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create([
            'user_id' => $user->id,
            'texte_brut' => 'Some receipt text',
            'statut' => 'en_attente',
        ]);

        ExtractionAgent::fake(function () {
            throw new \RuntimeException('API Error');
        });

        (new ExtraireDepensesDuRecu($recu->id))->handle();

        $this->assertDatabaseHas('recus', [
            'id' => $recu->id,
            'statut' => 'echoue',
        ]);

        $recu->refresh();
        $this->assertNotNull($recu->message_erreur);
        $this->assertDatabaseCount('depenses', 0);
    }

    #[Test]
    public function invalid_ai_response_is_handled_as_failure()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create([
            'user_id' => $user->id,
            'texte_brut' => 'Some receipt text',
            'statut' => 'en_attente',
        ]);

        ExtractionAgent::fake([[
            'articles' => [
                ['libelle' => 'Incomplete'],
            ],
            'total_estime' => 5.0,
            'devise' => 'MAD',
        ]]);

        (new ExtraireDepensesDuRecu($recu->id))->handle();

        $this->assertDatabaseHas('recus', [
            'id' => $recu->id,
            'statut' => 'echoue',
        ]);
        $this->assertDatabaseCount('depenses', 0);
    }
}
