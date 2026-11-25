<?php

namespace App\EventListener;

use App\Doctrine\EntrepriseFilter;
use App\Service\EntrepriseCourante;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Active le tri automatique par entreprise (EntrepriseFilter) au début de chaque page,
 * juste après la connexion (pare-feu, priorité 8) : on sait alors qui est connecté.
 */
#[AsEventListener(event: KernelEvents::REQUEST, priority: 5)]
class CloisonnementEntrepriseListener
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EntrepriseCourante $entrepriseCourante,
    ) {}

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->em->getFilters()
            ->enable(EntrepriseFilter::NOM)
            ->setParameter('entreprise', $this->entrepriseCourante->get()->getId());
    }
}
