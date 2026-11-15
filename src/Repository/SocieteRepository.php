<?php

namespace App\Repository;

use App\Entity\Societe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SocieteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Societe::class);
    }

    /** Fiche société unique — créée avec les valeurs par défaut si elle n'existe pas encore. */
    public function get(): Societe
    {
        $societe = $this->findOneBy([], ['id' => 'ASC']);

        if (!$societe) {
            $societe = new Societe();
            $this->getEntityManager()->persist($societe);
            $this->getEntityManager()->flush();
        }

        return $societe;
    }
}
