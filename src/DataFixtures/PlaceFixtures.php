<?php

namespace App\DataFixtures;

use App\Entity\Place;
use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $place = new Place();
            $place->setCity($this->getReference('city_'. rand(0,9)));
            $place->setName($faker->word());
            $place->setStreet($faker->streetName());
            $place->setLatitude($faker->latitude());
            $place->setLongitude($faker->longitude());
            $this->addReference('place_'. $i, $place);
            $manager->persist($place);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return [CityFixtures::class];
    }
}
