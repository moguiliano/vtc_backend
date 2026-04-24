<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Supprime le header X-Robots-Tag: noindex ajouté automatiquement par Symfony
 * lorsqu'une session démarre sur les pages publiques indexables.
 */
class SeoHeaderSubscriber implements EventSubscriberInterface
{
    // Pages publiques qui doivent être indexées par Google
    private const INDEXABLE_ROUTES = [
        'app_home',
        'app_taxi_marseille',
        'app_vtc_marseille',
        'app_taxi_aeroport',
        'app_taxi_gare',
        'app_taxi_calanques',
        'app_transport_seniors',
        'app_services',
        'app_contact',
    ];

    public static function getSubscribedEvents(): array
    {
        // Priorité négative = s'exécute APRÈS AbstractSessionListener (priorité 0)
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -10],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request  = $event->getRequest();
        $response = $event->getResponse();
        $route    = $request->attributes->get('_route');

        if (!in_array($route, self::INDEXABLE_ROUTES, true)) {
            return;
        }

        // Retirer le noindex ajouté par Symfony (session privée)
        $response->headers->remove('X-Robots-Tag');
        $response->headers->set('X-Robots-Tag', 'index, follow');

        // Rendre la réponse publique pour améliorer le cache
        $response->setPublic();
        $response->setMaxAge(3600);
    }
}
