<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\Forfait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ForfaitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forfait::class);
    }

    /** Forfaits actifs d'une entreprise, triés par ordre */
    public function findActifs(Entreprise $entreprise): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.actif = true')
            ->andWhere('f.entreprise = :e')->setParameter('e', $entreprise)
            ->orderBy('f.ordre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
