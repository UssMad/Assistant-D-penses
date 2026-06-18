<?php

namespace App\Ai;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

class ExtractionAgent implements Agent, Conversational, HasStructuredOutput, HasTools
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<PROMPT
Tu es un assistant spécialisé dans l'extraction de données de reçus de caisse écrits en Darija (arabe marocain) et en français.

Extrais les articles du reçu avec leur libellé, quantité, prix unitaire et catégorie.

Règles :
- Le libellé doit être en français (traduis-le si nécessaire)
- La catégorie doit être l'une des suivantes : alimentaire, boissons, hygiene, entretien, autre
- Si tu n'es pas sûr de la catégorie, utilise "autre"
- La quantité doit être un nombre entier
- Le prix unitaire doit être un nombre décimal
- Retourne aussi le total estimé et la devise
PROMPT;
    }

    public function messages(): iterable
    {
        return [];
    }

    public function tools(): iterable
    {
        return [];
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'articles' => $schema->array()->items(
                $schema->object([
                    'libelle' => $schema->string()->required()->description('Nom du produit en français'),
                    'quantite' => $schema->integer()->required()->description('Quantité achetée'),
                    'prix_unitaire' => $schema->number()->required()->description('Prix unitaire en MAD'),
                    'categorie' => $schema->string()->required()->enum([
                        'alimentaire', 'boissons', 'hygiene', 'entretien', 'autre',
                    ])->description('Catégorie du produit'),
                ])
            )->required()->description('Liste des articles extraits du reçu'),
            'total_estime' => $schema->number()->required()->description('Total estimé du reçu'),
            'devise' => $schema->string()->required()->description('Devise utilisée (MAD, EUR, etc.)'),
        ];
    }
}
