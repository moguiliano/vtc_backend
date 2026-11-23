<?php

namespace App\Twig;

use App\Entity\VehicleCategory;
use App\Service\Tarification;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/** `prix_depart(vehicle)` : prix « À partir de » de la grille de l'entreprise courante (onglet 2). */
class TarificationExtension extends AbstractExtension
{
    public function __construct(private readonly Tarification $tarification) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('prix_depart', fn (VehicleCategory $v): ?float => $this->tarification->prixDepart($v)),
        ];
    }
}
