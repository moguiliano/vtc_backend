<?php

namespace App\Repository;

use App\Entity\VehicleCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VehicleCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleCategory::class);
    }

    /** Retourne toutes les catégories actives triées par displayOrder */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.isActive = true')
            ->orderBy('v.displayOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Trouve une catégorie par son slug (ex: 'eco_berline') */
    public function findBySlug(string $slug): ?VehicleCategory
    {
        return $this->findOneBy(['slug' => $slug]);
    }
}
