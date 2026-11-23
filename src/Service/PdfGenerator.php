<?php

namespace App\Service;

use App\Entity\Entreprise;
use App\Entity\Facture;
use App\Entity\Reservation;
use App\Entity\Societe;
use App\Repository\SocieteRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Brique « Générer un PDF » (D-020).
 *
 * Bon de commande et facture, toujours au nom de l'entreprise du document (D-017) :
 * la fiche société est celle de l'entreprise de la réservation ou de la facture.
 * Les réglages Dompdf ne sont écrits qu'ici.
 */
class PdfGenerator
{
    public function __construct(
        private readonly Environment $twig,
        private readonly SocieteRepository $societeRepo,
        private readonly EntrepriseCourante $entrepriseCourante,
    ) {}

    public function bonDeCommande(Reservation $reservation): Response
    {
        return $this->reponse(
            $this->rendre('pdf/bon_commande.html.twig', [
                'reservation' => $reservation,
                'societe'     => $this->societe($reservation->getEntreprise()),
            ]),
            'bon-commande-' . $reservation->getId() . '.pdf',
        );
    }

    public function facture(Facture $facture): Response
    {
        return $this->reponse(
            $this->rendre('pdf/facture.html.twig', [
                'facture' => $facture,
                'societe' => $this->societe($facture->getEntreprise()),
            ]),
            $facture->getNumero() . '.pdf',
        );
    }

    private function societe(?Entreprise $entreprise): Societe
    {
        return $this->societeRepo->pourEntreprise($entreprise ?? $this->entrepriseCourante->get());
    }

    /** @param array<string, mixed> $contexte */
    private function rendre(string $template, array $contexte): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->twig->render($template, $contexte), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return (string) $dompdf->output();
    }

    /** Affiché dans le navigateur (inline), comme avant. */
    private function reponse(string $pdf, string $nomFichier): Response
    {
        return new Response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $nomFichier . '"',
        ]);
    }
}
