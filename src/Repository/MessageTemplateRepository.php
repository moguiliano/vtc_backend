<?php

namespace App\Repository;

use App\Entity\MessageTemplate;
use App\Enum\MessageTemplateType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessageTemplate>
 */
class MessageTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageTemplate::class);
    }

    /**
     * Modèles actifs d'un onglet de Partager, triés par catégorie puis par ordre.
     * @return MessageTemplate[]
     */
    public function findActifsParType(MessageTemplateType $type): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.categorie', 'c')->addSelect('c')
            ->where('c.type = :type')->setParameter('type', $type->value)
            ->andWhere('m.actif = true')
            ->orderBy('c.ordre', 'ASC')->addOrderBy('c.nom', 'ASC')
            ->addOrderBy('m.ordre', 'ASC')->addOrderBy('m.id', 'ASC')
            ->getQuery()->getResult();
    }
}
