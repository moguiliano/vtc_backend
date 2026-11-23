<?php

namespace App\Service;

use App\Entity\Entreprise;
use App\Repository\EntrepriseRepository;

/**
 * Brique « Quelle entreprise ? » (D-020).
 *
 * Aujourd'hui : toujours SIMPLICI CAB (entreprise n°1), sur le site public comme dans l'admin.
 * Demain (espaces abonnés, D-011 à D-017) : l'entreprise de l'utilisateur connecté.
 * Tout le code qui a besoin de savoir « pour qui » passe par ici, pour n'avoir qu'un seul endroit à changer.
 */
class EntrepriseCourante
{
    /** SIMPLICI CAB, créée par la migration Version20261123100000. */
    public const ID_PRINCIPALE = 1;

    public function __construct(private readonly EntrepriseRepository $repo) {}

    public function get(): Entreprise
    {
        return $this->repo->find(self::ID_PRINCIPALE)
            ?? throw new \LogicException('Entreprise n°1 introuvable : lancer les migrations (doctrine:migrations:migrate).');
    }
}
