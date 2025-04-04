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
    #[Route('', name: 'reservation_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();
            return $this->redirectToRoute('reservation_success');
        }

        return $this->render('home.html.twig', [
            'form' => $form->createView(),
            'here_api_key' => $this->getParameter('here_api_key') // ✅ injection de la clé HERE
        ]);
    }

    #[Route('/success', name: 'reservation_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->render('reservation/success.html.twig');
    }

    #[Route('/autocomplete', name: 'reservation_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request, HereMapsService $hereMapsService): JsonResponse
    {
        $query = $request->query->get('q');
        if (!$query) {
            return new JsonResponse(['error' => 'Le paramètre "q" est requis'], Response::HTTP_BAD_REQUEST);
        }

        $suggestions = $hereMapsService->autocompleteAddress($query);
        return new JsonResponse($suggestions);
    }
    #[Route('/test', name: 'reservation_test')]
    public function testReservation(): Response
    {
        return $this->render('reservation/reservation_test.html.twig', [
            'here_api_key' => $this->getParameter('here_api_key')
        ]);
        
        
        
    }



}
