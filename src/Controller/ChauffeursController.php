<?php

namespace App\Controller;

use App\Enum\TypeAbonnement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Page de présentation de l'espace pro aux chauffeurs (D-080, D-081).
 * Non référencée pour l'instant : elle se partage surtout dans les groupes WhatsApp de chauffeurs.
 * Adresse volontairement hors de /espace (sinon elle passerait par la porte des abonnés).
 */
class ChauffeursController extends AbstractController
{
    #[Route('/chauffeurs-vtc', name: 'app_chauffeurs_vtc', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('chauffeurs-vtc.html.twig', [
            'abonnements' => TypeAbonnement::cases(),
        ]);
    }
}
