<?php

namespace App\Enum;

/** Les deux abonnements (D-059, D-063) et leur prix TTC par mois (D-022). */
enum TypeAbonnement: string
{
    case CHAUFFEUR = 'chauffeur';
    case DISPATCHEUR = 'dispatcheur';

    public function label(): string
    {
        return match ($this) {
            self::CHAUFFEUR => 'Chauffeur indépendant',
            self::DISPATCHEUR => 'Dispatcheur',
        };
    }

    public function prixMensuelTtc(): int
    {
        return match ($this) {
            self::CHAUFFEUR => 10,
            self::DISPATCHEUR => 30,
        };
    }

    /** Pour les listes de choix : « Chauffeur indépendant — 10 € TTC / mois » => case */
    public static function choix(): array
    {
        $choix = [];
        foreach (self::cases() as $type) {
            $choix[sprintf('%s — %d € TTC / mois', $type->label(), $type->prixMensuelTtc())] = $type;
        }

        return $choix;
    }
}
