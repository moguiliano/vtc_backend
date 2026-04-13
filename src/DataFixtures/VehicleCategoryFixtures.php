<?php

namespace App\DataFixtures;

use App\Entity\VehicleCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VehicleCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            [
                'slug'                       => 'eco_berline',
                'label'                      => 'Eco-Berline',
                'description'                => 'Idéale pour vos trajets quotidiens, confortable et économique.',
                'basePriceUnderThreshold'    => 20.0,
                'pricePerKmUnderThreshold'   => 2.5,
                'basePriceOverThreshold'     => 45.0,
                'pricePerKmOverThreshold'    => 2.0,
                'thresholdKm'                => 10.0,
                'maxPassengers'              => 4,
                'luggageCapacity'            => 2,
                'displayOrder'               => 1,
            ],
            [
                'slug'                       => 'grand_coffre',
                'label'                      => 'Grand Coffre',
                'description'                => 'Parfait pour les voyageurs avec beaucoup de bagages.',
                'basePriceUnderThreshold'    => 30.0,
                'pricePerKmUnderThreshold'   => 2.5,
                'basePriceOverThreshold'     => 55.0,
                'pricePerKmOverThreshold'    => 2.0,
                'thresholdKm'                => 10.0,
                'maxPassengers'              => 4,
                'luggageCapacity'            => 4,
                'displayOrder'               => 2,
            ],
            [
                'slug'                       => 'berline',
                'label'                      => 'Berline Premium',
                'description'                => 'Le confort et l\'élégance pour vos déplacements professionnels.',
                'basePriceUnderThreshold'    => 35.0,
                'pricePerKmUnderThreshold'   => 0.0,
                'basePriceOverThreshold'     => 35.0,
                'pricePerKmOverThreshold'    => 3.1,
                'thresholdKm'                => 4.0,
                'maxPassengers'              => 4,
                'luggageCapacity'            => 3,
                'displayOrder'               => 3,
            ],
            [
                'slug'                       => 'van',
                'label'                      => 'Van',
                'description'                => 'Pour les groupes et les familles, spacieux et modulable.',
                'basePriceUnderThreshold'    => 63.0,
                'pricePerKmUnderThreshold'   => 0.0,
                'basePriceOverThreshold'     => 63.0,
                'pricePerKmOverThreshold'    => 3.2,
                'thresholdKm'                => 7.0,
                'maxPassengers'              => 8,
                'luggageCapacity'            => 6,
                'displayOrder'               => 4,
            ],
        ];

        foreach ($categories as $data) {
            $existing = $manager->getRepository(VehicleCategory::class)->findOneBy(['slug' => $data['slug']]);
            if ($existing) continue; // ne pas écraser si déjà en BDD

            $cat = (new VehicleCategory())
                ->setSlug($data['slug'])
                ->setLabel($data['label'])
                ->setDescription($data['description'])
                ->setBasePriceUnderThreshold($data['basePriceUnderThreshold'])
                ->setPricePerKmUnderThreshold($data['pricePerKmUnderThreshold'])
                ->setBasePriceOverThreshold($data['basePriceOverThreshold'])
                ->setPricePerKmOverThreshold($data['pricePerKmOverThreshold'])
                ->setThresholdKm($data['thresholdKm'])
                ->setMaxPassengers($data['maxPassengers'])
                ->setLuggageCapacity($data['luggageCapacity'])
                ->setDisplayOrder($data['displayOrder'])
                ->setIsActive(true);

            $manager->persist($cat);
        }

        $manager->flush();
    }
}
