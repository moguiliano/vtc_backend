<?php

namespace App\Controller;

// Entité Reservation = représente une ligne dans la table 'reservation'
use App\Entity\Contact;


// Formulaire Symfony lié à l'entité Reservation
use App\Form\ContactType;

// Permet de manipuler la base de données avec Doctrine
use App\Entity\Reservation;

// Récupère les données de la requête HTTP (formulaire, etc.)
use App\Form\ReservationType;

// Permet de retourner une réponse HTTP
use Doctrine\ORM\EntityManagerInterface;

// Annotation pour définir les routes
use Symfony\Component\HttpFoundation\Request;

// Classe de base pour tous les contrôleurs Symfony
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// ✅ AJOUTS
use App\Repository\ReservationRepository;
use App\Repository\VehicleCategoryRepository;
use App\Service\PhoneNormalizerService;
use App\Service\SmsNotifier;

final class HomeController extends AbstractController
{
    // Route pour la page des services
    #[Route('/services', name: 'app_services')]
    public function services(): Response
    {
        return $this->render('services.html.twig');
    }

    // Landing page SEO — "taxi marseille"
    #[Route('/taxi-marseille', name: 'app_taxi_marseille', methods: ['GET'])]
    public function taxiMarseille(VehicleCategoryRepository $vehicleRepo): Response
    {
        return $this->render('taxi-marseille.html.twig', [
            'vehicles' => $vehicleRepo->findAllActive(),
        ]);
    }

    // Page transport seniors & EHPAD
    #[Route('/transport-seniors-marseille', name: 'app_transport_seniors', methods: ['GET'])]
    public function transportSeniors(): Response
    {
        return $this->render('transport-seniors-marseille.html.twig');
    }

    // Route pour la page de contact
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $entityManager): Response
    {

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Créé automatiquement côté serveur
            $contact->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Votre message a bien été envoyé.');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, SmsNotifier $smsNotifier, PhoneNormalizerService $phoneNormalizer, VehicleCategoryRepository $vehicleRepo): Response
    {
        // Création d'une nouvelle entité Reservation (vide)
        $reservation = new Reservation();

        // Création du formulaire lié à cette entité
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        // Fusion des champs date + heure avant validation
        if ($form->isSubmitted() && $form->isValid()) {
            $date  = $form->get('dateDepart')->getData();   // peut être null
            $heure = $form->get('heureDepart')->getData();  // peut être null

            if ($date && $heure) {
                $fusion = new \DateTime();
                $fusion->setDate((int)$date->format('Y'), (int)$date->format('m'), (int)$date->format('d'));
                $fusion->setTime((int)$heure->format('H'), (int)$heure->format('i'));
                $reservation->setDateHeureDepart($fusion);
            }
            // 2) Récupère les champs non mappés (Tab3)
            $prenom      = (string) $request->request->get('prenom', '');
            // priorité au numéro normalisé, sinon brut
            $clientPhone = $request->request->get('fullPhone')
                ?? $request->request->get('guestPhone')
                ?? $request->request->get('clientPhone')
                ?? null;
            $clientPhone = $phoneNormalizer->normalize($clientPhone, 'FR');


            $entityManager->persist($reservation);
            $entityManager->flush();
            $smsNotifier->notifyReservation($reservation, $clientPhone, $prenom);

            // ✅ Redirection vers la page succès AVEC ID
            return $this->redirectToRoute('reservation_success_with_id', ['id' => $reservation->getId()]);
        }

        // Affichage du formulaire
        return $this->render('home.html.twig', [
            'form'     => $form->createView(),
            'vehicles' => $vehicleRepo->findAllActive(),
        ]);
    }
#[Route('/api/get-here-key', name: 'api_get_here_key', methods: ['GET'])]
    public function getHereKey(): JsonResponse
    {
        return $this->json([
            'key' => $this->getParameter('here_api_key')
        ]);
    }

    // ✅ AJOUT : page succès qui reçoit l'ID et passe l'entité au template

    #[Route('/reservation/{id}/success', name: 'reservation_success_with_id', methods: ['GET'])]
    public function reservationSuccessWithId(
        int $id,                            // ID de la réservation à afficher
        ReservationRepository $repo         // Repository pour la charger depuis la BDD
    ): Response {
        // On charge la réservation correspondante
        $reservation = $repo->find($id);

        // On passe l'entité au template pour affichage
        return $this->render('reservation/success.html.twig', [
            'reservation' => $reservation,  // Dans Twig => {{ reservation.id }}, etc.
        ]);
    }


    // ✅ AJOUT : endpoint pour le clic #confirmReservationBtn
    // Reçoit { prenom, clientPhone } en JSON depuis le front (Tab3 non mappé)
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

        $payload = json_decode($request->getContent() ?: '[]', true) ?? [];
        $clientPhone = !empty($payload['clientPhone']) ? $payload['clientPhone'] : null;
        $prenom      = !empty($payload['prenom']) ? $payload['prenom'] : null;

        $smsNotifier->notifyReservation($reservation, $clientPhone, $prenom);

        return new JsonResponse([
            'ok' => true,
            'redirect' => $this->generateUrl('reservation_success_with_id', ['id' => $reservation->getId()])
        ]);
    }
}
