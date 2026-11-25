<?php

namespace App\Repository;

use App\Entity\CompteAbonne;
use App\Entity\Entreprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CompteAbonne> */
class CompteAbonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompteAbonne::class);
    }

    public function pourEntreprise(Entreprise $entreprise): ?CompteAbonne
    {
        return $this->findOneBy(['entreprise' => $entreprise]);
    }
}
