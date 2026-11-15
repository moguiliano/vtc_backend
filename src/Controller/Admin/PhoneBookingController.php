<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Enum\ReservationStatus;
use App\Form\ReservationType;
use App\Repository\VehicleCategoryRepository;
use App\Service\PhoneNormalizerService;
use App\Service\SmsNotifier;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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
 *  - réservation créée directement au statut « confirmée ».
 */
#[IsGranted('ROLE_RESERVATIONS_EDIT')]
class PhoneBookingController extends AbstractController
{
    public function __construct(
        private VehicleCategoryRepository $vehicleRepo,
        private PhoneNormalizerService $phoneNormalizer,
        private SmsNotifier $smsNotifier,
        private AdminUrlGenerator $adminUrlGenerator,
    ) {}

    #[Route('/admin/reservation-telephone', name: 'admin_phone_booking', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $vehicles    = $this->vehicleRepo->findAllActive();
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
                $reservation->setGuestPrenom(mb_substr($client['prenom'], 0, 100));
                $reservation->setGuestTelephone(mb_substr($phone, 0, 25));
                $reservation->setGuestInfo(json_encode(array_filter([
                    'prenom' => $client['prenom'],
                    'phone'  => $phone,
                    'email'  => $client['email'] ?: null,
                    'source' => 'telephone',
                ])));
                $reservation->setInformationsComplementaires($client['infos'] ? mb_substr($client['infos'], 0, 500) : null);
                $reservation->setModeReglement(in_array($client['mode'], ['especes', 'carte_bancaire'], true) ? $client['mode'] : 'carte_bancaire');
                $reservation->setIsGuest(true);
                $reservation->setStatut(ReservationStatus::CONFIRMEE->value);
                $reservation->setCreatedAt(new \DateTimeImmutable());

                $em->persist($reservation);
                $em->flush();

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

    /**
     * @param array<string, mixed> $client
     * @param array<int, \App\Entity\VehicleCategory> $vehicles
     * @return string[]
     */
    private function validate(FormInterface $form, Reservation $reservation, array $client, array $vehicles): array
    {
        $errors = [];

        if (!$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
        }

        if (!trim((string) $reservation->getDepart()) || !trim((string) $reservation->getArrivee())) {
            $errors[] = 'Adresses de départ et d’arrivée obligatoires.';
        }
        if (!$reservation->getDistance()) {
            $errors[] = 'Le trajet n’a pas été calculé (onglet 1 → « Choisir le véhicule »).';
        }

        $slugs = array_map(fn ($v) => $v->getSlug(), $vehicles);
        if (!in_array($reservation->getTypeVehicule(), $slugs, true)) {
            $errors[] = 'Merci de choisir un véhicule.';
        }

        if ((float) $reservation->getPrix() <= 0) {
            $errors[] = 'Le prix doit être supérieur à 0 €.';
        }

        $dateHeure = $this->buildDateHeure($form);
        if (!$dateHeure) {
            $errors[] = 'Date et heure de départ obligatoires.';
        } elseif ($dateHeure <= new \DateTime()) {
            $errors[] = 'La date et l’heure de départ doivent être dans le futur.';
        }

        if ($client['prenom'] === '') {
            $errors[] = 'Le prénom du client est obligatoire.';
        }
        if (!preg_match('/^\+\d{8,15}$/', (string) $this->phoneNormalizer->normalize($client['telephone']))) {
            $errors[] = 'Numéro de téléphone invalide.';
        }
        if ($client['email'] !== '' && !filter_var($client['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Adresse email invalide.';
        }

        return array_values(array_unique($errors));
    }

    private function buildDateHeure(FormInterface $form): ?\DateTime
    {
        $date  = $form->get('dateDepart')->getData();
        $heure = $form->get('heureDepart')->getData();
        if (!$date || !$heure) {
            return null;
        }

        return (new \DateTime())
            ->setDate((int) $date->format('Y'), (int) $date->format('m'), (int) $date->format('d'))
            ->setTime((int) $heure->format('H'), (int) $heure->format('i'));
    }
}
