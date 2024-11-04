<?php

namespace App\DataFixtures;

use App\Entity\Trip;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TripFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        public
        function load(ObjectManager $manager): void
        {
            $faker = \Faker\Factory::create('fr_FR');
            for ($i = 0; $i < 10; $i++) {
                $trip = new Trip();
                $trip->setName($faker->title);
                $date = $faker->dateTime();
                $trip->setStartDateTime($date);
                $trip->setDuration($faker->numberBetween(30, 240));
                $trip->setRegistrationDeadline(date_modify($date, '-24 hours'));
                $trip->setNbRegistrationMax($faker->numberBetween(2, 150));
                $trip->$manager->persist($trip);
            }
            $manager->flush();
        }

        $manager->flush();
    }
}
