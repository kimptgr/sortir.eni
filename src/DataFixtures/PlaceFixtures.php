<?php

namespace App\DataFixtures;

use App\Entity\Place;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PlaceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for($i = 0; $i < 10; $i++) {
            $place = new Place();
            $place->setName($faker->title);
            $place->setStreet($faker->streetName);
            $place->setCity($this->getReference('city_'. rand(0,9)));
            $place->setLatitude($faker->latitude);
            $place->setLongitude($faker->longitude);
            $this->addReference('place_'. $i, $place);
            $manager->persist($place);
        }

        $manager->flush();
    }
}
