<?php

namespace App\Service;

use App\Doctrine\EntrepriseFilter;
use App\Entity\Entreprise;
use App\Entity\Reservation;
use App\Enum\StatutChauffeur;
use Doctrine\ORM\EntityManagerInterface;

/**
 * « Guichet » des courses confiées à un abonné (étape 2b, D-100 à D-107).
 *
 * Les réservations de l'émetteur (SIMPLICI CAB, plus tard un dispatcheur) sont cachées à l'abonné par
 * le tri automatique par entreprise. Ce service lève ce tri UNIQUEMENT pour lire les réservations dont
 * l'abonné est l'abonné retenu EN CE MOMENT (condition dans la requête), puis le remet aussitôt.
 * La seule écriture possible est la réponse du chauffeur (statut), sur une course qui est encore à lui.
 */
class CoursesRecues
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    /** @return Reservation[] courses confiées à l'abonné, la plus proche en haut */
    public function liste(Entreprise $abonne): array
    {
        return $this->sansTri($abonne, fn () => $this->em->getRepository(Reservation::class)->createQueryBuilder('r')
            ->where('r.abonneRetenu = :a')->setParameter('a', $abonne)
            ->orderBy('r.dateHeureDepart', 'DESC')
            ->getQuery()->getResult());
    }

    /** Une course confiée à CET abonné, sinon null (une course qui n'est pas à lui est « introuvable »). */
    public function trouver(Entreprise $abonne, int $id): ?Reservation
    {
        return $this->sansTri($abonne, fn () => $this->em->getRepository(Reservation::class)->createQueryBuilder('r')
            ->where('r.id = :id')->setParameter('id', $id)
            ->andWhere('r.abonneRetenu = :a')->setParameter('a', $abonne)
            ->getQuery()->getOneOrNullResult());
    }

    /** Nombre de courses qui attendent sa réponse (pastille du menu). */
    public function enAttenteDeReponse(Entreprise $abonne): int
    {
        return (int) $this->sansTri($abonne, fn () => $this->em->getRepository(Reservation::class)->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.abonneRetenu = :a')->setParameter('a', $abonne)
            ->andWhere('r.statutChauffeur IS NULL')
            ->getQuery()->getSingleScalarResult());
    }

    /**
     * Réponse du chauffeur (D-102, D-106). Refusée si la course n'est plus à lui ou si l'étape n'est pas permise.
     */
    public function repondre(Entreprise $abonne, int $id, StatutChauffeur $statut): bool
    {
        $r = $this->trouver($abonne, $id);
        if (!$r || !in_array($statut, StatutChauffeur::suivants($r->getStatutChauffeur()), true)) {
            return false;
        }
        $r->repondreChauffeur($statut, new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        $this->em->flush(); // « Modifiée par » = l'abonné (ReservationModificationListener)

        return true;
    }

    /** Lève le tri par entreprise le temps d'une lecture bornée à l'abonné, puis le remet. */
    private function sansTri(Entreprise $abonne, callable $lecture): mixed
    {
        $filtres = $this->em->getFilters();
        $actif = $filtres->isEnabled(EntrepriseFilter::NOM);
        if ($actif) {
            $filtres->disable(EntrepriseFilter::NOM);
        }
        try {
            return $lecture();
        } finally {
            if ($actif) {
                $filtres->enable(EntrepriseFilter::NOM)->setParameter('entreprise', $abonne->getId());
            }
        }
    }
}
