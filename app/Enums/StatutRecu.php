<?php

namespace App\Enums;

enum StatutRecu: string
{
    case en_attente = 'en_attente';
    case traite = 'traite';
    case echoue = 'echoue';

    public function label(): string
    {
        return match ($this) {
            self::en_attente => 'En attente',
            self::traite => 'Traité',
            self::echoue => 'Échoué',
        };
    }
}
