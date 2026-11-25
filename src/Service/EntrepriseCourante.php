<?php

namespace App\Service;

use App\Doctrine\EntrepriseFilter;
use App\Entity\CompteAbonne;
use App\Entity\Entreprise;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Brique « Quelle entreprise ? » (D-020).
 *
 * - Dans /espace, un abonné connecté : SON entreprise (D-076).
 * - Partout ailleurs (site public, /admin, commandes) : SIMPLICI CAB (entreprise n°1).
 * Les comptes abonnés n'existent que dans le pare-feu /espace : un abonné n'est jamais
 * « connecté » sur le site public ni dans /admin.
 */
class EntrepriseCourante
{
    /** SIMPLICI CAB, créée par la migration Version20261123100000. */
    public const ID_PRINCIPALE = 1;

    public function __construct(
        private readonly EntrepriseRepository $repo,
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
    ) {}

    public function get(): Entreprise
    {
        $user = $this->security->getUser();
        if ($user instanceof CompteAbonne) {
            return $user->getEntreprise()
                ?? throw new \LogicException('Compte abonné sans entreprise.');
        }

        return $this->principale();
    }

    public function principale(): Entreprise
    {
        return $this->repo->find(self::ID_PRINCIPALE)
            ?? throw new \LogicException('Entreprise n°1 introuvable : lancer les migrations (doctrine:migrations:migrate).');
    }

    public function estAbonne(): bool
    {
        return $this->security->getUser() instanceof CompteAbonne;
    }

    /**
     * Lever le tri automatique le temps d'une lecture, pour le super-admin seulement
     * (ex. afficher la société d'un abonné dans le menu « Abonnés »). Le tri est remis ensuite.
     *
     * @template T
     * @param callable(): T $lecture
     * @return T
     */
    public function sansCloisonnement(callable $lecture): mixed
    {
        if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            throw new \LogicException('Réservé au super-admin.');
        }

        $filtres = $this->em->getFilters();
        $actif = $filtres->isEnabled(EntrepriseFilter::NOM);
        if ($actif) {
            $filtres->disable(EntrepriseFilter::NOM);
        }

        try {
            return $lecture();
        } finally {
            if ($actif) {
                $filtres->enable(EntrepriseFilter::NOM)->setParameter('entreprise', $this->get()->getId());
            }
        }
    }
}
