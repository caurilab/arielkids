<?php

namespace App\Enums;

enum Audience: string
{
    case EnfantFille = 'enfant_fille';
    case EnfantGarcon = 'enfant_garcon';
    case Bebe = 'bebe';
    case Femme = 'femme';
    case Homme = 'homme';

    public function label(): string
    {
        return match ($this) {
            self::EnfantFille => 'Fille',
            self::EnfantGarcon => 'Garçon',
            self::Bebe => 'Bébé',
            self::Femme => 'Femme',
            self::Homme => 'Homme',
        };
    }

    public function isChild(): bool
    {
        return in_array($this, [self::EnfantFille, self::EnfantGarcon, self::Bebe], true);
    }

    /** @return list<self> */
    public static function children(): array
    {
        return [self::EnfantFille, self::EnfantGarcon, self::Bebe];
    }

    /** @return list<self> */
    public static function adults(): array
    {
        return [self::Femme, self::Homme];
    }
}
