<?php

namespace App\Tests\Service;

use App\Entity\Entreprise;
use App\Entity\GrilleTarif;
use App\Entity\VehicleCategory;
use App\Repository\GrilleTarifRepository;
use App\Service\EntrepriseCourante;
use App\Service\Tarification;
use PHPUnit\Framework\TestCase;

/** Brique « Calculer un prix » (D-020) : mêmes résultats que l'ancien calcul de HereMapsService. */
class TarificationTest extends TestCase
{
    private function grille(string $slug, float $seuil, float $baseSous, float $kmSous, float $baseSur, float $kmSur): GrilleTarif
    {
        return (new GrilleTarif())
            ->setCategorie((new VehicleCategory())->setSlug($slug)->setLabel($slug))
            ->setThresholdKm($seuil)
            ->setBasePriceUnderThreshold($baseSous)->setPricePerKmUnderThreshold($kmSous)
            ->setBasePriceOverThreshold($baseSur)->setPricePerKmOverThreshold($kmSur);
    }

    private function tarification(): Tarification
    {
        $entreprise = (new Entreprise())->setNom('Test');
        (new \ReflectionProperty(Entreprise::class, 'id'))->setValue($entreprise, 1);

        $repo = $this->createMock(GrilleTarifRepository::class);
        $repo->method('findProposees')->willReturn([
            $this->grille('eco_berline', 10, 20, 2.5, 45, 2),   // grille SIMPLICI CAB actuelle
            $this->grille('berline', 4, 35, 0, 35, 3.1),
        ]);
        $courante = $this->createMock(EntrepriseCourante::class);
        $courante->method('get')->willReturn($entreprise);

        return new Tarification($repo, $courante, 20.0, 20.0, 23, 7);
    }

    public function testPrixDeJourSousLeSeuil(): void
    {
        // 20 + 3 km × 2,5 = 27,5 ; commission 20 % = 5,5
        $this->assertSame(
            ['prix_total' => 27.5, 'commission' => 5.5, 'net_chauffeur' => 22.0, 'majoration_nuit' => false],
            $this->tarification()->estimerPrix(3, 'eco_berline', 10),
        );
    }

    public function testPrixDeNuitAuDelaDuSeuil(): void
    {
        // 45 + (25 − 10) × 2 = 75 ; nuit +20 % = 90
        $prix = $this->tarification()->estimerPrix(25, 'eco_berline', 23);
        $this->assertSame(90.0, $prix['prix_total']);
        $this->assertTrue($prix['majoration_nuit']);
    }

    public function testHeuresDeNuit(): void
    {
        $t = $this->tarification();
        $this->assertTrue($t->estNuit(23));
        $this->assertTrue($t->estNuit(6));
        $this->assertFalse($t->estNuit(7));
        $this->assertFalse($t->estNuit(22));
    }

    public function testToutesLesCategoriesProposeesParSlug(): void
    {
        $tout = $this->tarification()->estimerToutesCategories(12.5, 10);
        $this->assertSame(['eco_berline', 'berline'], array_keys($tout));
        $this->assertSame(35 + (12.5 - 4) * 3.1, $tout['berline']['prix_total']);
    }

    public function testCategorieNonProposeeDonneNull(): void
    {
        $this->assertNull($this->tarification()->estimerPrix(10, 'van', 10));
    }

    public function testPrixDepart(): void
    {
        $this->assertSame(20.0, $this->tarification()->prixDepart((new VehicleCategory())->setSlug('eco_berline')));
    }
}
