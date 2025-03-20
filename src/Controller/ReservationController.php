<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\HttpClient;
use App\Entity\Reservation;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/reservation/init', name: 'reservation_init', methods: ['POST'])]
public function initReservation(Request $request, EntityManagerInterface $entityManager): Response
{
    $data = json_decode($request->getContent(), true);
    $depart = $data['depart'];
    $arrivee = $data['arrivee'];
    $stopLieu = $data['stopLieu'] ?? null;
    $typeVehicule = $data['typeVehicule'];

    // Simule le calcul du prix
    $distance = rand(5, 50);
    $duree = rand(10, 90);
    $prix = $distance * $this->getVehiclePricing($typeVehicule);

    $reservation = new Reservation();
    $reservation->setDepart($depart);
    $reservation->setArrivee($arrivee);
    $reservation->setStopLieu($stopLieu);
    $reservation->setTypeVehicule($typeVehicule);
    $reservation->setDistance($distance);
    $reservation->setDuree($duree);
    $reservation->setPrix($prix);

    $entityManager->persist($reservation);
    $entityManager->flush();

    return new JsonResponse([
        'message' => 'Réservation enregistrée',
        'prix' => $prix,
        'distance' => $distance,
        'duree' => $duree,
        'reservationId' => $reservation->getId()
    ], Response::HTTP_OK);
}
