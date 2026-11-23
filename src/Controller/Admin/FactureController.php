<?php

namespace App\Controller\Admin;

use App\Repository\FactureRepository;
use App\Service\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_RESERVATIONS_VIEW')]
class FactureController extends AbstractController
{
    #[Route('/admin/factures/{id}/pdf', name: 'admin_facture_pdf', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function pdf(int $id, FactureRepository $repo, PdfGenerator $pdf): Response
    {
        $facture = $repo->find($id) ?? throw $this->createNotFoundException('Facture introuvable.');

        return $pdf->facture($facture);
    }
}
