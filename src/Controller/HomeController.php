<?php

namespace App\Controller;

// Entité Reservation = représente une ligne dans la table 'reservation'
use App\Entity\Reservation;

// Formulaire Symfony lié à l'entité Reservation
use App\Form\ReservationType;

// Permet de manipuler la base de données avec Doctrine
use Doctrine\ORM\EntityManagerInterface;

// Récupère les données de la requête HTTP (formulaire, etc.)
use Symfony\Component\HttpFoundation\Request;

// Permet de retourner une réponse HTTP
use Symfony\Component\HttpFoundation\Response;

// Annotation pour définir les routes
use Symfony\Component\Routing\Attribute\Route;

// Classe de base pour tous les contrôleurs Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Ce contrôleur gère la page d’accueil, les services, le contact et le formulaire de réservation
final class HomeController extends AbstractController
{


    // Route pour la page des services
    #[Route('/services', name: 'app_services')]
    public function services(): Response
    {
        return $this->render('services.html.twig');
    }

    // Route pour la page de contact
    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('contact.html.twig');
    }

    // ATTENTION : cette route est identique à celle du haut ('/'), ce qui pose un conflit
    // Il ne faut **pas** avoir deux routes avec le même chemin ET le même nom
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création d'une nouvelle entité Reservation (vide)
        $reservation = new Reservation();

        // Création du formulaire lié à cette entité
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        // Fusion des champs date + heure avant validation
        if ($form->isSubmitted()) {
            $date = $form->get('dateDepart')->getData();
            $heure = $form->get('heureDepart')->getData();

                $fusion = new \DateTime();
                $fusion->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
                $fusion->setTime($heure->format('H'), $heure->format('i'));
                $reservation->setDateHeureDepart($fusion); // ✅ AVANT isValid()

            // Validation du formulaire après fusion
                $entityManager->persist($reservation);
                $entityManager->flush();

                return $this->redirectToRoute('reservation_success');
            
        }

        // Affichage du formulaire
        return $this->render('home.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
