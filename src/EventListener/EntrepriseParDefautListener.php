<?php

namespace App\EventListener;

use App\Entity\AppartientAEntreprise;
use App\Service\EntrepriseCourante;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

/**
 * Filet de sécurité (D-020) : toute donnée étiquetable enregistrée sans entreprise
 * reçoit l'entreprise courante, pour ne jamais casser l'enregistrement (colonne obligatoire).
 */
#[AsDoctrineListener(event: Events::prePersist)]
class EntrepriseParDefautListener
{
    public function __construct(private readonly EntrepriseCourante $entrepriseCourante) {}

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof AppartientAEntreprise && $entity->getEntreprise() === null) {
            $entity->setEntreprise($this->entrepriseCourante->get());
        }
    }
}
