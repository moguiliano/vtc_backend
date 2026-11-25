<?php

namespace App\Enum;

/**
 * Statut côté chauffeur d'une course confiée (D-102), synchronisé avec le statut client (D-106).
 * Null = course proposée, le chauffeur n'a pas encore répondu.
 */
enum StatutChauffeur: string
{
    case PRIS = 'pris';
    case ARRIVE = 'arrive';
    case A_BORD = 'a_bord';
    case TERMINE = 'termine';
    case RETIRE = 'retire';

    /** Texte du bouton côté chauffeur. */
    public function bouton(): string
    {
        return match ($this) {
            self::PRIS => 'Je prends la course',
            self::ARRIVE => 'Je suis arrivé',
            self::A_BORD => 'Client à bord',
            self::TERMINE => 'Course terminée',
            self::RETIRE => 'Je me retire',
        };
    }

    /** Résumé affiché (espace et admin). */
    public function label(): string
    {
        return match ($this) {
            self::PRIS => 'A pris la course',
            self::ARRIVE => 'Arrivé',
            self::A_BORD => 'Client à bord',
            self::TERMINE => 'Terminée',
            self::RETIRE => 'S\'est retiré',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::PRIS => 'info',
            self::ARRIVE, self::A_BORD => 'primary',
            self::TERMINE => 'success',
            self::RETIRE => 'danger',
        };
    }

    /** Statut client correspondant (D-106) : « Je me retire » remet « Confirmée », jamais « Annulée ». */
    public function statutClient(): ReservationStatus
    {
        return match ($this) {
            self::PRIS => ReservationStatus::CHAUFFEUR_ASSIGNE,
            self::ARRIVE => ReservationStatus::CHAUFFEUR_ARRIVE,
            self::A_BORD => ReservationStatus::CLIENT_A_BORD,
            self::TERMINE => ReservationStatus::TERMINEE,
            self::RETIRE => ReservationStatus::CONFIRMEE,
        };
    }

    /**
     * Boutons proposés au chauffeur selon où il en est.
     * @return self[]
     */
    public static function suivants(?self $actuel): array
    {
        return match ($actuel) {
            null => [self::PRIS, self::RETIRE],
            self::PRIS => [self::ARRIVE, self::RETIRE],
            self::ARRIVE => [self::A_BORD, self::RETIRE],
            self::A_BORD => [self::TERMINE],
            self::TERMINE, self::RETIRE => [],
        };
    }
}
