<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\VehicleCategoryRepository;
use App\Service\HereMapsService;
use App\Service\ReservationAccess;
use App\Service\ReservationCreator;
use App\Service\SmsNotifier;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\ForfaitRepository;
use App\Repository\ReservationRepository;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    public function __construct(
        private HereMapsService $hereMapsService,
        private ReservationAccess $access,
        private ReservationCreator $creator,
    ) {}

    #[Route('', name: 'reservation_index', methods: ['GET', 'POST'])]
    public function index(Request $request, VehicleCategoryRepository $vehicleRepo): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setDateHeureDepart(ReservationCreator::fusionnerDateHeure(
                $form->get('dateDepart')->getData(),
                $form->get('heureDepart')->getData(),
            ));

            $this->creator->enregistrer($reservation);
            $this->access->grant($reservation->getId());

            // Appel AJAX depuis reservation.js → retourner juste l'ID
            if ($request->isXmlHttpRequest()) {
                if (!$reservation->getTypeVehicule()) {
                    return new JsonResponse(['error' => 'Merci de sélectionner un type de véhicule.'], 400);
                }
                return new JsonResponse(['id' => $reservation->getId()]);
            }

            // Appel normal (fallback) → re-render avec tab3
            return $this->render('reservation/index.html.twig', [
                'form'         => $form->createView(),
                'here_api_key' => $this->getParameter('here_api_key'),
                'vehicles'     => $vehicleRepo->findAllActive(),
                'reservation'  => $reservation,
                'show_tab3'    => true,
            ]);
        }

        return $this->render('reservation/index.html.twig', [
            'form'         => $form->createView(),
            'here_api_key' => $this->getParameter('here_api_key'),
            'vehicles'     => $vehicleRepo->findAllActive(),
        ]);
    }

    #[Route('/{id}/confirm', name: 'reservation_confirm', methods: ['POST'])]
    public function confirmReservation(
        int $id,
        Request $request,
        ReservationRepository $repo,
        EntityManagerInterface $em,
        SmsNotifier $smsNotifier
    ): JsonResponse {
        $reservation = $repo->find($id);
        if (!$reservation || !($this->access->ownsInSession($id) || $this->access->isAdmin())) {
            return new JsonResponse(['ok' => false, 'error' => 'Reservation introuvable'], 404);
        }

        $payload     = json_decode($request->getContent() ?: '[]', true) ?? [];
        $clientPhone = $payload['clientPhone'] ?? null;
        $prenom      = $payload['prenom'] ?? null;
        $infos       = $payload['informationsComplementaires'] ?? null;
        $modeReglement = $payload['modeReglement'] ?? null;
        $bypassOtp   = (bool) ($payload['bypassOtp'] ?? false);

        // OTP bypass: only allowed for authenticated ROLE_ADMIN or ROLE_REGULATEUR
        if ($bypassOtp && !$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_REGULATEUR')) {
            return new JsonResponse(['ok' => false, 'error' => 'Non autorisé'], 403);
        }

        // Infos client (brique « Créer une réservation »)
        $this->creator->renseignerClient($reservation, $prenom, $clientPhone, $infos, $modeReglement);
        $em->flush();

        $smsNotifier->notifyReservation($reservation, $clientPhone);

        return new JsonResponse([
            'ok'       => true,
            'redirect' => $this->access->url('reservation_success', $reservation->getId()),
        ]);
    }

    #[Route('/{id}/success', name: 'reservation_success', methods: ['GET'])]
    public function reservationSuccess(int $id, Request $request, ReservationRepository $repo): Response
    {
        $reservation = $repo->find($id);

        if (!$reservation || !$this->access->isAllowed($request, $id)) {
            $this->addFlash('error', 'Reservation introuvable.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('reservation/success.html.twig', [
            'reservation' => $reservation,
            'pdf_url'     => $this->access->url('reservation_pdf', $id),
        ]);
    }

    #[Route('/quick-book', name: 'reservation_quick_book', methods: ['POST'])]
    public function quickBook(Request $request, ForfaitRepository $forfaitRepo): JsonResponse
    {
        $data      = json_decode($request->getContent(), true) ?? [];
        $forfaitId = (int) ($data['forfaitId'] ?? 0);
        $prenom    = trim($data['prenom'] ?? '');
        $phone     = trim($data['telephone'] ?? '');
        $mode      = $data['modeReglement'] ?? null;

        if (!$forfaitId || !$prenom || !$phone) {
            return new JsonResponse(['error' => 'Prénom, téléphone et forfait sont obligatoires.'], 400);
        }

        $forfait = $forfaitRepo->find($forfaitId);
        if (!$forfait || !$forfait->isActif()) {
            return new JsonResponse(['error' => 'Forfait introuvable.'], 404);
        }

        $reservation = $this->creator->depuisForfait($forfait, $prenom, $phone, $mode, (int) ($data['offsetMinutes'] ?? 0));
        $this->access->grant($reservation->getId());

        return new JsonResponse([
            'redirect' => $this->access->url('reservation_success', $reservation->getId()),
        ]);
    }

    #[Route('/{id}/bon-commande.pdf', name: 'reservation_pdf', methods: ['GET'])]
    public function pdf(int $id, Request $request, ReservationRepository $repo, SocieteRepository $societeRepo): Response
    {
        $reservation = $repo->find($id);
        if (!$reservation || !$this->access->isAllowed($request, $id)) {
            throw $this->createNotFoundException('Réservation introuvable.');
        }

        $html = $this->renderView('pdf/bon_commande.html.twig', [
            'reservation' => $reservation,
            'societe'     => $societeRepo->pourEntreprise($reservation->getEntreprise()),
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="bon-commande-' . $id . '.pdf"',
            ]
        );
    }

    #[Route('/calculate-trip', name: 'reservation_calculate_trip', methods: ['POST'])]
    public function calculateTrip(Request $request): JsonResponse
    {
        $data    = json_decode($request->getContent(), true);
        $pickup  = $data['pickup'] ?? null;
        $dropoff = $data['dropoff'] ?? null;
        $stop    = ($data['stopEnabled'] ?? false) ? ($data['stop'] ?? null) : null;
        $heure   = (int) ($data['heure'] ?? date('G'));

        if (!$pickup || !$dropoff) {
            return new JsonResponse(['error' => 'Adresse de depart ou arrivee manquante.'], 400);
        }

        $distanceInfos = $this->hereMapsService->getDistanceAndDurationWithStop($pickup, $dropoff, $stop);
        if (isset($distanceInfos['error'])) {
            return new JsonResponse($distanceInfos, 400);
        }

        $prixParCategorie = $this->hereMapsService->estimerToutesCategoriesActives(
            $distanceInfos['distance_km'],
            $heure
        );

        return new JsonResponse([
            'distance_km'  => $distanceInfos['distance_km'],
            'duration_min' => $distanceInfos['duration_min'],
            'prix'         => $prixParCategorie,
        ]);
    }
}
