<?php

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Protège les pages publiques d'une réservation (succès, bon de commande PDF, confirmation).
 *
 * Les numéros de réservation se suivent : sans protection, n'importe qui pourrait
 * ouvrir la réservation d'un autre client en changeant le numéro dans l'adresse.
 *
 * Accès autorisé si :
 *  - l'utilisateur est admin ou régulateur ;
 *  - ou la réservation a été créée dans la même session navigateur ;
 *  - ou le lien contient le bon jeton (?t=...), calculé avec APP_SECRET.
 */
class ReservationAccess
{
    private const SESSION_KEY = 'owned_reservations';

    public function __construct(
        #[Autowire('%kernel.secret%')] private string $secret,
        private RequestStack $requestStack,
        private Security $security,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    /** À appeler juste après la création d'une réservation par un visiteur. */
    public function grant(int $id): void
    {
        $session = $this->requestStack->getSession();
        $ids = $session->get(self::SESSION_KEY, []);
        $ids[] = $id;
        $session->set(self::SESSION_KEY, array_slice(array_unique($ids), -20));
    }

    public function isAdmin(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_REGULATEUR');
    }

    public function ownsInSession(int $id): bool
    {
        $session = $this->requestStack->getSession();

        return in_array($id, $session->get(self::SESSION_KEY, []), true);
    }

    public function token(int $id): string
    {
        return substr(hash_hmac('sha256', 'reservation:' . $id, $this->secret), 0, 32);
    }

    public function isAllowed(Request $request, int $id): bool
    {
        if ($this->isAdmin() || $this->ownsInSession($id)) {
            return true;
        }

        return hash_equals($this->token($id), (string) $request->query->get('t', ''));
    }

    /** Lien signé (ex. 'reservation_success', 'reservation_pdf'). */
    public function url(string $route, int $id): string
    {
        return $this->urlGenerator->generate($route, ['id' => $id, 't' => $this->token($id)]);
    }
}
