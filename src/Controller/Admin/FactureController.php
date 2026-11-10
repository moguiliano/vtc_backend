<?php

namespace App\Controller\Admin;

use App\Repository\FactureRepository;
use App\Repository\SocieteRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_RESERVATIONS_VIEW')]
class FactureController extends AbstractController
{
    #[Route('/admin/factures/{id}/pdf', name: 'admin_facture_pdf', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function pdf(int $id, FactureRepository $repo, SocieteRepository $societeRepo): Response
    {
        $facture = $repo->find($id) ?? throw $this->createNotFoundException('Facture introuvable.');

        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->renderView('pdf/facture.html.twig', ['facture' => $facture, 'societe' => $societeRepo->get()]), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $facture->getNumero() . '.pdf"',
        ]);
    }
}
