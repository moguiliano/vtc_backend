<?php

namespace App\EventListener;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Trace de la dernière modification d'une réservation (D-068) :
 * « Modifiée par » = email du compte admin connecté, ou « site » (client, OTP…).
 * « Modifiée le » = date et heure de la modification.
 * La création n'est pas une modification : la date de création existe déjà.
 */
#[AsDoctrineListener(event: Events::onFlush)]
class ReservationModificationListener
{
    public function __construct(private readonly Security $security) {}

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em  = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof Reservation) {
                continue;
            }

            $entity->marquerModifiee(
                $this->security->getUser()?->getUserIdentifier() ?? 'site',
                new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')), // heure de Marseille (comme D-032)
            );
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Reservation::class), $entity);
        }
    }
}
