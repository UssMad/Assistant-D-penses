<?php

namespace App\Jobs;

use App\Ai\ExtractionAgent;
use App\Models\Depenses;
use App\Models\Recu;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExtraireDepensesDuRecu implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $recuId
    ) {}

    public function handle(): void
    {
        $recu = Recu::findOrFail($this->recuId);

        try {
            $agent = new ExtractionAgent;
            $response = $agent->prompt($recu->texte_brut);
            $data = $response->toArray();

            $this->validateResponse($data);

            foreach ($data['articles'] as $article) {
                Depenses::create([
                    'recu_id' => $recu->id,
                    'libelle' => $article['libelle'],
                    'quantite' => $article['quantite'],
                    'prix_unitaire' => $article['prix_unitaire'],
                    'categorie' => $article['categorie'],
                ]);
            }

            $recu->update([
                'statut' => 'traite',
                'payload_ia' => $data,
            ]);
        } catch (\Throwable $e) {
            $recu->update([
                'statut' => 'echoue',
                'message_erreur' => $e instanceof \Illuminate\Validation\ValidationException
                    ? 'La réponse de l\'IA est invalide : ' . $e->getMessage()
                    : 'Erreur lors de l\'extraction des dépenses. Veuillez réessayer.',
            ]);
        }
    }

    private function validateResponse(array $data): void
    {
        if (!isset($data['articles']) || !is_array($data['articles'])) {
            throw new \RuntimeException('Le champ "articles" est manquant ou invalide.');
        }

        foreach ($data['articles'] as $i => $article) {
            if (!isset($article['libelle'], $article['quantite'], $article['prix_unitaire'], $article['categorie'])) {
                throw new \RuntimeException("L'article $i est incomplet.");
            }
        }
    }
}
