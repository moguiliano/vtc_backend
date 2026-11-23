<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    /** Prochain numéro chronologique de l'année pour une entreprise (D-003, D-017) : FAC-2026-0001 */
    public function nextNumero(Entreprise $entreprise, \DateTimeInterface $date): string
    {
        $prefix = 'FAC-' . $date->format('Y') . '-';

        $last = $this->createQueryBuilder('f')
            ->select('f.numero')
            ->where('f.numero LIKE :p')
            ->andWhere('f.entreprise = :e')
            ->setParameter('p', $prefix . '%')
            ->setParameter('e', $entreprise)
            ->orderBy('f.numero', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $next = $last ? ((int) substr($last['numero'], strlen($prefix))) + 1 : 1;

        return sprintf('%s%04d', $prefix, $next);
    }
}
