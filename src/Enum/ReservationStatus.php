<?php

namespace App\Enum;

enum ReservationStatus: string
{
    case EN_ATTENTE        = 'en_attente';
    case CONFIRMEE         = 'confirmee';
    case CHAUFFEUR_ASSIGNE = 'chauffeur_assigne';
    case CHAUFFEUR_ARRIVE  = 'chauffeur_arrive';
    case CLIENT_A_BORD     = 'client_a_bord';
    case TERMINEE          = 'terminee';
    case ANNULEE           = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE        => 'En attente',
            self::CONFIRMEE         => 'Confirmée',
            self::CHAUFFEUR_ASSIGNE => 'Chauffeur assigné',
            self::CHAUFFEUR_ARRIVE  => 'Chauffeur arrivé',
            self::CLIENT_A_BORD     => 'Client à bord',
            self::TERMINEE          => 'Terminée',
            self::ANNULEE           => 'Annulée',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::EN_ATTENTE        => 'warning',
            self::CONFIRMEE         => 'info',
            self::CHAUFFEUR_ASSIGNE => 'primary',
            self::CHAUFFEUR_ARRIVE  => 'secondary',
            self::CLIENT_A_BORD     => 'dark',
            self::TERMINEE          => 'success',
            self::ANNULEE           => 'danger',
        };
    }

    /** Returns ['label' => 'value', ...] for ChoiceType / EasyAdmin */
    public static function choices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->label()] = $case->value;
        }
        return $choices;
    }
}
