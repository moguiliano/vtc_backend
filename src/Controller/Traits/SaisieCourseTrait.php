<?php

namespace App\Controller\Traits;

use App\Entity\Reservation;
use App\Service\ReservationCreator;
use Symfony\Component\Form\FormInterface;

/**
 * Contrôles de la saisie d'une course (réservation par téléphone de l'admin et « Nouvelle course »
 * de l'espace abonné, D-103) : un seul endroit à modifier.
 * La classe qui l'utilise doit avoir une propriété $phoneNormalizer (PhoneNormalizerService).
 */
trait SaisieCourseTrait
{
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

        // Date passée acceptée (D-032) : course oubliée puis dispatchée à la main, saisie après coup.
        if (!$this->buildDateHeure($form)) {
            $errors[] = 'Date et heure de départ obligatoires.';
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
        return ReservationCreator::fusionnerDateHeure(
            $form->get('dateDepart')->getData(),
            $form->get('heureDepart')->getData(),
        );
    }
}
