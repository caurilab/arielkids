<?php

namespace App\Support;

/**
 * Source unique de formatage des montants FCFA côté serveur.
 * Prix stockés en entiers, affichés « 12 500 F CFA » (espaces insécables).
 */
class Currency
{
    public static function format(int $amount): string
    {
        return number_format($amount, 0, ',', "\u{202F}")."\u{00A0}F\u{00A0}CFA";
    }
}
