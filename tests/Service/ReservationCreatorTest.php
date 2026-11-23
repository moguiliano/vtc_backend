<?php

namespace App\Tests\Service;

use App\Entity\Entreprise;
use App\Entity\Reservation;
use App\Service\EntrepriseCourante;
use App\Service\ReservationCreator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

/** Brique « Créer une réservation » (D-020). */
class ReservationCreatorTest extends TestCase
{
    private Entreprise $entreprise;

    private function creator(): ReservationCreator
    {
        $this->entreprise = (new Entreprise())->setNom('SIMPLICI CAB');
        $courante = $this->createMock(EntrepriseCourante::class);
        $courante->method('get')->willReturn($this->entreprise);

        return new ReservationCreator($this->createMock(EntityManagerInterface::class), $courante);
    }

    public function testEnregistrerColleLEntrepriseCourante(): void
    {
        $r = $this->creator()->enregistrer(new Reservation());
        $this->assertSame($this->entreprise, $r->getEntreprise());
    }

    public function testEnregistrerAvecUneAutreEntreprise(): void
    {
        $autre = (new Entreprise())->setNom('Abonné');
        $r = $this->creator()->enregistrer(new Reservation(), $autre);
        $this->assertSame($autre, $r->getEntreprise());
    }

    public function testRenseignerClient(): void
    {
        $r = $this->creator()->renseignerClient(new Reservation(), 'Karim', '+33612345678', 'Valise', 'especes', ['source' => 'telephone']);
        $this->assertSame('Karim', $r->getGuestPrenom());
        $this->assertSame('+33612345678', $r->getGuestTelephone());
        $this->assertSame('especes', $r->getModeReglement());
        $this->assertSame('{"prenom":"Karim","phone":"+33612345678","source":"telephone"}', $r->getGuestInfo());
        $this->assertTrue($r->getIsGuest());
    }

    public function testModeDeReglementInconnuDevientCarte(): void
    {
        $r = $this->creator()->renseignerClient(new Reservation(), 'A', '+33600000000', null, 'cheque');
        $this->assertSame('carte_bancaire', $r->getModeReglement());
    }

    public function testFusionnerDateHeure(): void
    {
        $d = ReservationCreator::fusionnerDateHeure(new \DateTime('2026-12-24'), new \DateTime('1970-01-01 18:45'));
        $this->assertSame('2026-12-24 18:45', $d->format('Y-m-d H:i'));
        $this->assertNull(ReservationCreator::fusionnerDateHeure(null, new \DateTime()));
    }
}
