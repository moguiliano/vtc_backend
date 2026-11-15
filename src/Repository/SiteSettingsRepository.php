<?php

namespace App\Repository;

use App\Entity\SiteSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SiteSettings>
 */
class SiteSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteSettings::class);
    }

    /** La ligne de réglages, ou null si la table est vide. */
    public function findCurrent(): ?SiteSettings
    {
        return $this->findOneBy([], ['id' => 'ASC']);
    }
}
