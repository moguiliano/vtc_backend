<?php

namespace App\Repository;

use App\Entity\Forfait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ForfaitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forfait::class);
    }

    /** Retourne les forfaits actifs triés par ordre */
    public function findActifs(): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.actif = true')
            ->orderBy('f.ordre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
