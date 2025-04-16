<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Service\HereMapsService;
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
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Récupérer séparément date et heure
            $date = $form->get('dateDepart')->getData();    // DateTime (00:00:00)
            $heure = $form->get('heureDepart')->getData();  // DateTime (par défaut à 1970-01-01)
        
            // Fusionner les deux en un seul DateTime
            $dateHeureDepart = new \DateTime();
            $dateHeureDepart->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
            $dateHeureDepart->setTime($heure->format('H'), $heure->format('i'));
            
            $entityManager->persist($reservation);
            $entityManager->flush();
            return $this->redirectToRoute('reservation_success');
        }

        return $this->render('reservation/reservation_test.html.twig', [
            'form' => $form->createView(),
            'here_api_key' => $this->getParameter('here_api_key')
        ]);
    }

    #[Route('/success', name: 'reservation_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->render('reservation/success.html.twig');
    }

    #[Route('/calculate-trip', name: 'reservation_calculate_trip', methods: ['POST'])]
    public function calculateTrip(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $pickup = $data['pickup'] ?? null;
        $dropoff = $data['dropoff'] ?? null;
        $stop = ($data['stopEnabled'] ?? false) ? ($data['stop'] ?? null) : null;
        $heure = (int) ($data['heure'] ?? date('G'));

        if (!$pickup || !$dropoff) {
            return new JsonResponse(['error' => 'Adresse de départ ou arrivée manquante.'], 400);
        }

        $distanceInfos = $this->hereMapsService->getDistanceAndDurationWithStop($pickup, $dropoff, $stop);
        if (isset($distanceInfos['error'])) {
            return new JsonResponse($distanceInfos, 400);
        }

        $prixParCategorie = [];
        $categories = ['eco_berline', 'grand_coffre', 'berline', 'van'];
        foreach ($categories as $cat) {
            $prix = $this->hereMapsService->estimerPrix($distanceInfos['distance_km'], $cat, $heure);
            if ($prix) {
                $prixParCategorie[$cat] = $prix;
            }
        }

        return new JsonResponse([
            'distance_km' => $distanceInfos['distance_km'],
            'duration_min' => $distanceInfos['duration_min'],
            'prix' => $prixParCategorie
        ]);
    }
}
