<?php

namespace App\Enum;

/**
 * Les 3 modes de partage d'une course (D-023).
 */
enum MessageTemplateType: string
{
    case CHAUFFEUR = 'chauffeur';
    case GROUPE    = 'groupe';
    case CLIENT    = 'client';

    public function label(): string
    {
        return match ($this) {
            self::CHAUFFEUR => 'Partage complet (chauffeur)',
            self::GROUPE    => 'Partage partiel (groupe)',
            self::CLIENT    => 'Message client',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::CHAUFFEUR => 'primary',
            self::GROUPE    => 'warning',
            self::CLIENT    => 'success',
        };
    }

    /** ['label' => 'value', ...] pour EasyAdmin */
    public static function choices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->label()] = $case->value;
        }
        return $choices;
    }
}
