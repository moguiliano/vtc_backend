<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\Societe;
use App\Service\EntrepriseCourante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SocieteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Societe::class);
    }

    /**
     * Fiche société d'une entreprise (D-017) — créée si elle n'existe pas encore.
     * Les valeurs par défaut de Societe sont celles de SIMPLICI CAB : un abonné reçoit une fiche vide (D-015).
     */
    public function pourEntreprise(Entreprise $entreprise): Societe
    {
        $societe = $this->findOneBy(['entreprise' => $entreprise]);

        if (!$societe) {
            $societe = $entreprise->getId() === EntrepriseCourante::ID_PRINCIPALE
                ? (new Societe())->setEntreprise($entreprise)
                : Societe::vierge($entreprise);
            $this->getEntityManager()->persist($societe);
            $this->getEntityManager()->flush();
        }

        return $societe;
    }

    /** Fiche déjà envoyée par l'abonné, sans en créer (null si pas encore remplie). */
    public function trouverPourEntreprise(Entreprise $entreprise): ?Societe
    {
        return $this->findOneBy(['entreprise' => $entreprise]);
    }
}
