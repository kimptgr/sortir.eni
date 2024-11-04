<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Place;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $cities = $manager->getRepository(City::class)->findAll();
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $place = new Place();
            $place->setCity($faker->randomElement($cities));
            $place->setName($faker->word());
            $place->setStreet($faker->streetAddress());
            $place->setLatitude($faker->latitude());
            $place->setLongitude($faker->longitude());
            $manager->persist($place);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return [CityFixtures::class];
    }
}
