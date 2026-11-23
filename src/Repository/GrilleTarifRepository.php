<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\GrilleTarif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<GrilleTarif> */
class GrilleTarifRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrilleTarif::class);
    }

    /**
     * Grilles proposées par l'entreprise : grille cochée ET catégorie active (super-admin),
     * triées comme les catégories (displayOrder).
     *
     * @return GrilleTarif[]
     */
    public function findProposees(Entreprise $entreprise): array
    {
        return $this->createQueryBuilder('g')
            ->join('g.categorie', 'c')->addSelect('c')
            ->where('g.entreprise = :e')->setParameter('e', $entreprise)
            ->andWhere('g.actif = true')
            ->andWhere('c.isActive = true')
            ->orderBy('c.displayOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
