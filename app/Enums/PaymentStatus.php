<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case EnAttente = 'en_attente';
    case Paye = 'paye';
    case Echoue = 'echoue';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Paye => 'Payé',
            self::Echoue => 'Échoué',
        };
    }
}
