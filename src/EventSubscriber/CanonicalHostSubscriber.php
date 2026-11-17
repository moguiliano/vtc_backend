<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirige www.dzencar.fr vers https://dzencar.fr (301) pour éviter que Google
 * indexe deux adresses pour la même page.
 *
 * Volontairement limité au nom de domaine : la redirection http → https se règle
 * dans le Manager Infomaniak (« forcer HTTPS »), car derrière le proxy d'Infomaniak
 * le code ne sait pas toujours si la requête d'origine était en https (risque de boucle).
 */
class CanonicalHostSubscriber implements EventSubscriberInterface
{
    private const CANONICAL_HOST = 'dzencar.fr';

    public static function getSubscribedEvents(): array
    {
        // Avant le routage et la sécurité
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 256],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (strtolower($request->getHost()) !== 'www.' . self::CANONICAL_HOST) {
            return;
        }

        $event->setResponse(new RedirectResponse(
            'https://' . self::CANONICAL_HOST . $request->getRequestUri(),
            301
        ));
    }
}
