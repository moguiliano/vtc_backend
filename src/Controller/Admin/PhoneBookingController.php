<?php

namespace App\Controller\Admin;

use App\Controller\Traits\SaisieCourseTrait;
use App\Entity\Reservation;
use App\Enum\ReservationStatus;
use App\Form\ReservationType;
use App\Service\PhoneNormalizerService;
use App\Service\ReservationCreator;
use App\Service\Tarification;
use App\Service\SmsNotifier;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Réservation saisie par l'admin pour un client qui appelle au téléphone.
 *
 * Reprend le parcours public en 3 onglets (tab1 + tab2 partagés, tab3 propre à l'admin) :
 *  - pas d'OTP ;
 *  - prix calculé proposé mais modifiable ;
 *  - SMS de confirmation au client optionnel ;
 *  - réservation créée directement au statut « confirmée » ;
 *  - date et heure préremplies avec maintenant, date passée acceptée (D-032).
 */
#[IsGranted('ROLE_RESERVATIONS_EDIT')]
class PhoneBookingController extends AbstractController
{
    use SaisieCourseTrait; // contrôles communs avec « Nouvelle course » de l'espace abonné (D-103)

    public function __construct(
        private Tarification $tarification,
        private PhoneNormalizerService $phoneNormalizer,
        private SmsNotifier $smsNotifier,
        private AdminUrlGenerator $adminUrlGenerator,
        private ReservationCreator $creator,
    ) {}

    #[Route('/admin/reservation-telephone', name: 'admin_phone_booking', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $vehicles    = $this->tarification->categoriesProposees();
        $reservation = new Reservation();
        $form        = $this->createForm(ReservationType::class, $reservation, [
            'action' => $this->generateUrl('admin_phone_booking'),
        ]);
        $form->handleRequest($request);

        $errors = [];
        $client = [
            'prenom'    => trim((string) $request->request->get('prenom', '')),
            'telephone' => trim((string) $request->request->get('telephone', '')),
            'email'     => trim((string) $request->request->get('email', '')),
            'infos'     => trim((string) $request->request->get('informationsComplementaires', '')),
            'mode'      => (string) $request->request->get('modeReglement', 'carte_bancaire'),
            'sendSms'   => $request->request->getBoolean('sendSms'),
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
                    ['email' => $client['email'] ?: null, 'source' => 'telephone'],
                );
                $reservation->setStatut(ReservationStatus::CONFIRMEE->value);
                $reservation->setCreatedAt(new \DateTimeImmutable());

                $this->creator->enregistrer($reservation);

                if ($client['sendSms']) {
                    $this->smsNotifier->notifyReservation($reservation, $phone, $client['prenom']);
                }

                $this->addFlash('success', $client['sendSms']
                    ? 'Réservation créée et SMS de confirmation envoyé au client.'
                    : 'Réservation créée (aucun SMS envoyé).');

                return $this->redirect($this->adminUrlGenerator
                    ->setController(ReservationCrudController::class)
                    ->setAction('detail')
                    ->setEntityId($reservation->getId())
                    ->generateUrl());
            }
        }

        return $this->render('admin/phone_booking/index.html.twig', [
            'form'     => $form->createView(),
            'vehicles' => $vehicles,
            'errors'   => $errors,
            'client'   => $client,
        ]);
    }
}
