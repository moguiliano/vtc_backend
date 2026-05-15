<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\VehicleCategoryRepository;
use App\Service\HereMapsService;
use App\Service\SmsNotifier;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    public function __construct(private HereMapsService $hereMapsService) {}

    #[Route('', name: 'reservation_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, VehicleCategoryRepository $vehicleRepo): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date  = $form->get('dateDepart')->getData();
            $heure = $form->get('heureDepart')->getData();

            $dateHeureDepart = new \DateTime();
            $dateHeureDepart->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
            $dateHeureDepart->setTime($heure->format('H'), $heure->format('i'));
            $reservation->setDateHeureDepart($dateHeureDepart);

            $entityManager->persist($reservation);
            $entityManager->flush();

            // Re-render with the new reservation so Tab3 can show the ID
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
        if (!$reservation) {
            return new JsonResponse(['ok' => false, 'error' => 'Reservation introuvable'], 404);
        }

        $payload     = json_decode($request->getContent() ?: '[]', true) ?? [];
        $clientPhone = $payload['clientPhone'] ?? null;
        $prenom      = $payload['prenom'] ?? null;
        $infos       = $payload['informationsComplementaires'] ?? null;
        $modeReglement = $payload['modeReglement'] ?? 'carte_bancaire';
        $bypassOtp   = (bool) ($payload['bypassOtp'] ?? false);

        // OTP bypass: only allowed for authenticated ROLE_ADMIN or ROLE_REGULATEUR
        if ($bypassOtp && !$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_REGULATEUR')) {
            return new JsonResponse(['ok' => false, 'error' => 'Non autorisé'], 403);
        }

        // Persist client info and new fields
        $reservation->setGuestPrenom($prenom ? mb_substr($prenom, 0, 100) : null);
        $reservation->setGuestTelephone($clientPhone ? mb_substr($clientPhone, 0, 25) : null);
        $reservation->setGuestInfo(json_encode(['prenom' => $prenom, 'phone' => $clientPhone]));
        $reservation->setInformationsComplementaires($infos ? mb_substr($infos, 0, 500) : null);
        $reservation->setModeReglement(in_array($modeReglement, ['especes', 'carte_bancaire'], true) ? $modeReglement : 'carte_bancaire');
        $reservation->setIsGuest(true);
        $em->flush();

        $smsNotifier->notifyReservation($reservation, $clientPhone);

        return new JsonResponse([
            'ok'       => true,
            'redirect' => $this->generateUrl('reservation_success', ['id' => $reservation->getId()]),
        ]);
    }

    #[Route('/{id}/success', name: 'reservation_success', methods: ['GET'])]
    public function reservationSuccess(int $id, ReservationRepository $repo): Response
    {
        $reservation = $repo->find($id);

        if (!$reservation) {
            $this->addFlash('error', 'Reservation introuvable.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('reservation/success.html.twig', ['reservation' => $reservation]);
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
