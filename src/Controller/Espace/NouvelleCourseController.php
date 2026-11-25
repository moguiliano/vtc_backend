<?php

namespace App\Controller\Espace;

use App\Controller\Traits\SaisieCourseTrait;
use App\Entity\Reservation;
use App\Enum\EtatAbonne;
use App\Enum\ReservationStatus;
use App\Form\ReservationType;
use App\Service\EntrepriseCourante;
use App\Service\HereMapsService;
use App\Service\PhoneNormalizerService;
use App\Service\ReservationCreator;
use App\Service\Tarification;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * « Nouvelle course » de l'abonné (étape 2a, D-103) : le formulaire de la réservation par téléphone,
 * avec SES véhicules et SES prix (EntrepriseCourante = son entreprise), sans SMS (D-098).
 * La course est enregistrée au nom de son entreprise (ReservationCreator), jamais de SIMPLICI CAB.
 */
#[IsGranted('ROLE_ABONNE')]
class NouvelleCourseController extends AbstractController
{
    use SaisieCourseTrait;

    public function __construct(
        private readonly Tarification $tarification,
        private readonly PhoneNormalizerService $phoneNormalizer,
        private readonly ReservationCreator $creator,
        private readonly EntrepriseCourante $entrepriseCourante,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {}

    #[Route('/espace/nouvelle-course', name: 'espace_nouvelle_course', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        if ($refus = $this->refus()) {
            return $refus;
        }

        $vehicles = $this->tarification->categoriesProposees();
        if (!$vehicles) {
            $this->addFlash('warning', 'Activez d\'abord au moins un véhicule « Proposé » dans Mes prix.');

            return $this->redirectToRoute('espace_mes_prix_index');
        }

        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation, [
            'action' => $this->generateUrl('espace_nouvelle_course'),
        ]);
        $form->handleRequest($request);
        $errors = [];

        $client = [
            'prenom'    => trim((string) $request->request->get('prenom', '')),
            'telephone' => trim((string) $request->request->get('telephone', '')),
            'email'     => trim((string) $request->request->get('email', '')),
            'infos'     => trim((string) $request->request->get('informationsComplementaires', '')),
            'mode'      => (string) $request->request->get('modeReglement', 'carte_bancaire'),
            'sendSms'   => false,
        ];

        if ($form->isSubmitted()) {
            $errors = $this->validate($form, $reservation, $client, $vehicles);

            if (!$errors) {
                $phone = $this->phoneNormalizer->normalize($client['telephone']);
                $reservation->setDateHeureDepart($this->buildDateHeure($form));
                $reservation->setStopOption((bool) $reservation->getStopLieu());
                $reservation->setSiegeBebe((bool) $reservation->isSiegeBebe());
                $reservation->setPrix(round((float) $reservation->getPrix(), 2));
                $this->creator->renseignerClient(
                    $reservation, $client['prenom'], $phone, $client['infos'], $client['mode'],
                    ['email' => $client['email'] ?: null, 'source' => 'espace_abonne'],
                );
                $reservation->setStatut(ReservationStatus::CONFIRMEE->value);
                $reservation->setCreatedAt(new \DateTimeImmutable());
                $this->creator->enregistrer($reservation, $this->entrepriseCourante->get());

                $this->addFlash('success', 'Course enregistrée.');

                return $this->redirect($this->adminUrlGenerator
                    ->setDashboard(EspaceDashboardController::class)
                    ->setController(MesCoursesCrudController::class)
                    ->setAction('detail')
                    ->setEntityId($reservation->getId())
                    ->generateUrl());
            }
        }

        return $this->render('espace/nouvelle_course.html.twig', [
            'form'     => $form->createView(),
            'vehicles' => $vehicles,
            'errors'   => $errors,
            'client'   => $client,
        ]);
    }

    /** Calcul du trajet et des prix avec SA grille (D-103) ; même réponse que /reservation/calculate-trip. */
    #[Route('/espace/calcul-trajet', name: 'espace_calcul_trajet', methods: ['POST'])]
    public function calculTrajet(Request $request, HereMapsService $here): JsonResponse
    {
        if ($this->refus()) {
            return new JsonResponse(['error' => 'Accès refusé.'], 403);
        }

        $data    = json_decode($request->getContent(), true) ?: [];
        $pickup  = $data['pickup'] ?? null;
        $dropoff = $data['dropoff'] ?? null;
        $stop    = ($data['stopEnabled'] ?? false) ? ($data['stop'] ?? null) : null;
        $heure   = (int) ($data['heure'] ?? date('G'));

        if (!$pickup || !$dropoff) {
            return new JsonResponse(['error' => 'Adresse de depart ou arrivee manquante.'], 400);
        }

        $infos = $here->getDistanceAndDurationWithStop($pickup, $dropoff, $stop);
        if (isset($infos['error'])) {
            return new JsonResponse($infos, 400);
        }

        return new JsonResponse([
            'distance_km'  => $infos['distance_km'],
            'duration_min' => $infos['duration_min'],
            'prix'         => $this->tarification->estimerToutesCategories($infos['distance_km'], $heure),
        ]);
    }

    /** Espace en attente : pas de courses ; lecture seule : pas de création (D-014). */
    private function refus(): ?Response
    {
        $e = $this->entrepriseCourante->get();
        if ($e->isEnAttente()) {
            return $this->redirectToRoute('espace_accueil');
        }
        if ($e->getEtat() === EtatAbonne::LECTURE_SEULE) {
            $this->addFlash('warning', 'Votre espace est en lecture seule : vous ne pouvez pas créer de course.');

            return $this->redirectToRoute('espace_mes_courses_index');
        }

        return null;
    }
}
