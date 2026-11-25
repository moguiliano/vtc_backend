<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Enum\EtatAbonne;
use App\Service\EntrepriseCourante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Entreprise> */
class EntrepriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entreprise::class);
    }

    /** Abonnés validés (essai ou actif), par nom : ceux qui peuvent être « chauffeur retenu » (D-083). */
    public function qbAbonnesValides(string $alias = 'e'): QueryBuilder
    {
        return $this->createQueryBuilder($alias)
            ->andWhere("$alias.id <> :principale")
            ->andWhere("$alias.etat IN (:etats)")
            ->setParameter('principale', EntrepriseCourante::ID_PRINCIPALE)
            ->setParameter('etats', [EtatAbonne::ESSAI->value, EtatAbonne::ACTIF->value])
            ->orderBy("$alias.nom", 'ASC');
    }

    /** @return Entreprise[] */
    public function abonnesValides(): array
    {
        return $this->qbAbonnesValides()->getQuery()->getResult();
    }

    /** Tous les abonnés (jamais SIMPLICI CAB), pour le filtre de la liste des réservations. */
    public function qbAbonnes(string $alias = 'e'): QueryBuilder
    {
        return $this->createQueryBuilder($alias)
            ->andWhere("$alias.id <> :principale")
            ->setParameter('principale', EntrepriseCourante::ID_PRINCIPALE)
            ->orderBy("$alias.nom", 'ASC');
    }
}
