<?php

namespace App\Enums;

enum CategorieDepense: string
{
    case alimentaire = 'alimentaire';
    case boissons = 'boissons';
    case hygiene = 'hygiene';
    case entretien = 'entretien';
    case autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::alimentaire => 'Alimentaire',
            self::boissons => 'Boissons',
            self::hygiene => 'Hygiène',
            self::entretien => 'Entretien',
            self::autre => 'Autre',
        };
    }
}
