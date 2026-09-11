<?php

namespace App\Enums;

enum OrderStatus: string
{
    case EnAttente = 'en_attente';
    case Payee = 'payee';
    case EnPreparation = 'en_preparation';
    case Expediee = 'expediee';
    case Livree = 'livree';
    case Annulee = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Payee => 'Payée',
            self::EnPreparation => 'En préparation',
            self::Expediee => 'Expédiée',
            self::Livree => 'Livrée',
            self::Annulee => 'Annulée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EnAttente => 'sun',
            self::Payee => 'sky',
            self::EnPreparation => 'lav',
            self::Expediee => 'mint',
            self::Livree => 'mint',
            self::Annulee => 'brand',
        };
    }
}
