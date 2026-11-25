<?php

namespace App\Enum;

/** États d'une entreprise abonnée (D-014, D-015, D-022). SIMPLICI CAB est « active ». */
enum EtatAbonne: string
{
    case EN_ATTENTE = 'en_attente';
    case ESSAI = 'essai';
    case ACTIF = 'actif';
    case LECTURE_SEULE = 'lecture_seule';

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente de validation',
            self::ESSAI => 'Essai gratuit',
            self::ACTIF => 'Actif',
            self::LECTURE_SEULE => 'Lecture seule',
        };
    }

    public static function choix(): array
    {
        $choix = [];
        foreach (self::cases() as $etat) {
            $choix[$etat->label()] = $etat;
        }

        return $choix;
    }
}
