<?php

namespace Tests\Unit;

use App\Enums\CategorieDepense;
use App\Enums\StatutRecu;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    #[Test]
    public function statut_recu_label_returns_french_string()
    {
        $this->assertEquals('En attente', StatutRecu::en_attente->label());
        $this->assertEquals('Traité', StatutRecu::traite->label());
        $this->assertEquals('Échoué', StatutRecu::echoue->label());
    }

    #[Test]
    public function statut_recu_is_backed_string_enum()
    {
        $this->assertEquals('en_attente', StatutRecu::en_attente->value);
        $this->assertSame(StatutRecu::traite, StatutRecu::from('traite'));
    }

    #[Test]
    public function categorie_depense_label_returns_french_string()
    {
        $this->assertEquals('Alimentaire', CategorieDepense::alimentaire->label());
        $this->assertEquals('Boissons', CategorieDepense::boissons->label());
        $this->assertEquals('Hygiène', CategorieDepense::hygiene->label());
        $this->assertEquals('Entretien', CategorieDepense::entretien->label());
        $this->assertEquals('Autre', CategorieDepense::autre->label());
    }

    #[Test]
    public function categorie_depense_is_backed_string_enum()
    {
        $this->assertEquals('alimentaire', CategorieDepense::alimentaire->value);
        $this->assertSame(CategorieDepense::hygiene, CategorieDepense::from('hygiene'));
    }
}
