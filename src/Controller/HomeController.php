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
use App\Repository\ForfaitRepository;
use App\Repository\ReservationRepository;
use App\Repository\VehicleCategoryRepository;
use App\Service\PhoneNormalizerService;
use App\Service\SmsNotifier;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class HomeController extends AbstractController
{
    // Route pour la page des services
    #[Route('/services', name: 'app_services')]
    public function services(): Response
    {
        return $this->render('services.html.twig');
    }

    // Landing page SEO — "taxi marseille"
    #[Route('/taxi-marseille', name: 'app_taxi_marseille', methods: ['GET', 'POST'])]
    public function taxiMarseille(Request $request, EntityManagerInterface $entityManager, VehicleCategoryRepository $vehicleRepo, SmsNotifier $smsNotifier, PhoneNormalizerService $phoneNormalizer): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date  = $form->get('dateDepart')->getData();
            $heure = $form->get('heureDepart')->getData();

            if ($date && $heure) {
                $fusion = new \DateTime();
                $fusion->setDate((int)$date->format('Y'), (int)$date->format('m'), (int)$date->format('d'));
                $fusion->setTime((int)$heure->format('H'), (int)$heure->format('i'));
                $reservation->setDateHeureDepart($fusion);
            }

            $prenom      = (string) $request->request->get('prenom', '');
            $clientPhone = $request->request->get('fullPhone')
                ?? $request->request->get('guestPhone')
                ?? $request->request->get('clientPhone')
                ?? null;
            $clientPhone = $phoneNormalizer->normalize($clientPhone, 'FR');

            $entityManager->persist($reservation);
            $entityManager->flush();
            $smsNotifier->notifyReservation($reservation, $clientPhone, $prenom);

            return $this->redirectToRoute('reservation_success_with_id', ['id' => $reservation->getId()]);
        }

        return $this->render('taxi-marseille.html.twig', [
            'form'     => $form->createView(),
            'vehicles' => $vehicleRepo->findAllActive(),
        ]);
    }

    // Landing page SEO — "taxi aéroport marseille"
    #[Route('/taxi-aeroport-marseille', name: 'app_taxi_aeroport', methods: ['GET'])]
    public function taxiAeroport(ForfaitRepository $forfaitRepo): Response
    {
        return $this->render('taxi-aeroport-marseille.html.twig', [
            'forfaits' => $forfaitRepo->findActifs(),
        ]);
    }

    // Landing page SEO — "taxi gare saint-charles marseille"
    #[Route('/taxi-gare-saint-charles', name: 'app_taxi_gare', methods: ['GET'])]
    public function taxiGare(ForfaitRepository $forfaitRepo): Response
    {
        return $this->render('taxi-gare-saint-charles.html.twig', [
            'forfaits' => $forfaitRepo->findActifs(),
        ]);
    }

    // Page transport seniors & EHPAD
    #[Route('/transport-seniors-marseille', name: 'app_transport_seniors', methods: ['GET'])]
    public function transportSeniors(): Response
    {
        return $this->render('transport-seniors-marseille.html.twig');
    }

    // Landing page SEO — "taxi calanques marseille"
    #[Route('/taxi-calanques-marseille', name: 'app_taxi_calanques', methods: ['GET'])]
    public function taxiCalanques(ForfaitRepository $forfaitRepo): Response
    {
        return $this->render('taxi-calanques-marseille.html.twig', [
            'forfaits' => $forfaitRepo->findActifs(),
        ]);
    }

    // Landing page SEO — "vtc marseille"
    #[Route('/vtc-marseille', name: 'app_vtc_marseille', methods: ['GET'])]
    public function vtcMarseille(ForfaitRepository $forfaitRepo): Response
    {
        return $this->render('vtc-marseille.html.twig', [
            'forfaits' => $forfaitRepo->findActifs(),
        ]);
    }

    // Pages légales
    #[Route('/mentions-legales', name: 'app_mentions_legales', methods: ['GET'])]
    public function mentionsLegales(): Response
    {
        return $this->render('mentions-legales.html.twig');
    }

    #[Route('/politique-confidentialite', name: 'app_confidentialite', methods: ['GET'])]
    public function politiqueConfidentialite(): Response
    {
        return $this->render('politique-confidentialite.html.twig');
    }

    // Route pour la page de contact
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($contact);
            $entityManager->flush();

            // Envoi email de notification à l'admin
            try {
                $adminEmail = (new Email())
                    ->from('noreply@dzencar.fr')
                    ->to('mohamedguiliano@yahoo.com')
                    ->replyTo($contact->getEmail())
                    ->subject('📩 Nouveau message ZenCAR — ' . $contact->getName())
                    ->html(
                        '<h2 style="color:#ff5630;">Nouveau message via le formulaire ZenCAR</h2>'
                        . '<table style="font-family:Arial,sans-serif;font-size:15px;border-collapse:collapse;width:100%">'
                        . '<tr><td style="padding:8px;font-weight:bold;width:120px">Nom</td><td style="padding:8px">' . htmlspecialchars($contact->getName()) . '</td></tr>'
                        . '<tr style="background:#f5f5f5"><td style="padding:8px;font-weight:bold">Email</td><td style="padding:8px"><a href="mailto:' . $contact->getEmail() . '">' . htmlspecialchars($contact->getEmail()) . '</a></td></tr>'
                        . '<tr><td style="padding:8px;font-weight:bold">Téléphone</td><td style="padding:8px"><a href="tel:' . $contact->getPhone() . '">' . htmlspecialchars($contact->getPhone()) . '</a></td></tr>'
                        . '<tr style="background:#f5f5f5"><td style="padding:8px;font-weight:bold;vertical-align:top">Message</td><td style="padding:8px">' . nl2br(htmlspecialchars($contact->getMessage())) . '</td></tr>'
                        . '</table>'
                        . '<p style="color:#999;font-size:13px;margin-top:20px">Envoyé le ' . $contact->getCreatedAt()->format('d/m/Y à H:i') . '</p>'
                    );

                $mailer->send($adminEmail);

                // Email de confirmation au client
                $clientEmail = (new Email())
                    ->from('noreply@dzencar.fr')
                    ->to($contact->getEmail())
                    ->subject('✅ ZenCAR — Votre message a bien été reçu')
                    ->html(
                        '<h2 style="color:#ff5630;">Bonjour ' . htmlspecialchars($contact->getName()) . ',</h2>'
                        . '<p style="font-family:Arial,sans-serif;font-size:15px;">Votre message a bien été reçu. Nous vous répondrons dans les plus brefs délais.</p>'
                        . '<p style="font-family:Arial,sans-serif;font-size:15px;">Pour toute urgence, appelez directement le <strong><a href="tel:+33674039694">06 74 03 96 94</a></strong>.</p>'
                        . '<p style="font-family:Arial,sans-serif;font-size:15px;color:#888;">L\'équipe ZenCAR Marseille</p>'
                    );

                $mailer->send($clientEmail);
            } catch (\Exception $e) {
                // Ne pas bloquer si l'email échoue — le message est déjà en BDD
            }

            $this->addFlash('success', 'Votre message a bien été envoyé. Nous vous répondrons rapidement.');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, SmsNotifier $smsNotifier, PhoneNormalizerService $phoneNormalizer, VehicleCategoryRepository $vehicleRepo, ForfaitRepository $forfaitRepo): Response
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
            'forfaits' => $forfaitRepo->findActifs(),
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
