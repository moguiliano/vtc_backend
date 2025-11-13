<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Service\HereMapsService;
use App\Service\SmsNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ReservationRepository;
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

        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
            'here_api_key' => $this->getParameter('here_api_key')
        ]);
    }

  
    #[Route('/reservation/{id}/confirm', name: 'reservation_confirm', methods: ['POST'])]
    public function confirmReservation(
        int $id,
        Request $request,
        ReservationRepository $repo,
        SmsNotifier $smsNotifier
    ): JsonResponse {
        $reservation = $repo->find($id);
        if (!$reservation) {
            return new JsonResponse(['ok' => false, 'error' => 'Reservation introuvable'], 404);
        }

        // Optionnel : numéro client envoyé explicitement depuis le front (sinon pris depuis l’entité)
        $payload = json_decode($request->getContent() ?: '[]', true) ?? [];
        $clientPhone = $payload['clientPhone'] ?? null;

        // Envoi des 2 SMS (client + toi)
        $smsNotifier->notifyReservation($reservation, $clientPhone);

        // On renvoie une redirection front vers la page de succès
        return new JsonResponse([
            'ok' => true,
            'redirect' => $this->generateUrl('reservation_success', ['id' => $reservation->getId()])
        ]);
    }

    #[Route('/reservation/{id}/success', name: 'reservation_success', methods: ['GET'])]
    public function reservationSuccess(
        int $id,
        ReservationRepository $repo
    ) {
        $reservation = $repo->find($id);

        // Si jamais pas d’ID valide (tests/design), on affiche des données fictives
        if (!$reservation) {
            $fake = [
                'immediacy' => 'Immédiat',
                'prenom' => 'Karim',
                'depart' => 'Gare Saint-Charles, Marseille',
                'arrivee' => 'Aéroport Marseille-Provence',
                'pickup' => (new \DateTimeImmutable('+15 minutes'))->format('d/m/Y H:i'),
                'stop' => '—',
                'siege' => 'Non',
                'vehicule' => 'Eco-berline',
                'distance' => '22,5 km',
                'duree' => '28 min',
                'prix' => '42 €',
                'created' => (new \DateTimeImmutable())->format('d/m/Y H:i'),
            ];
            return $this->render('reservation/success.html.twig', ['reservation' => null, 'fake' => $fake]);
        }

        return $this->render('reservation/success.html.twig', ['reservation' => $reservation, 'fake' => null]);
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
