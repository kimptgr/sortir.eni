<?php

namespace App\DataFixtures;

use App\Entity\Trip;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TripFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
//        for ($i = 0; $i < 100; $i++) {
//            $trip = new Trip();
//            $trip->setName($faker->words(5, true));
//            //$date = $faker->dateTime();
//            $date =$faker->dateTimeBetween('-1 week', '+1 week');
//            $trip->setStartDateTime($date);
//            $trip->setDuration($faker->numberBetween(30, 240));
//            $registrationDeadline = clone $date;
//            $registrationDeadline->modify('-24 hours');
//            $trip->setRegistrationDeadline($registrationDeadline);
//            $trip->setNbRegistrationMax($faker->numberBetween(2, 150));
//            $trip->setInfo($faker->paragraph());
//            $trip->setState($this->getReference('state_'.rand(0,5)));
//            $trip->setRelativeCampus($this->getReference("campus_" .rand(0, 4)));
//            $trip->addParticipant($this->getReference("participant_" .rand(0, 4)));
//            $trip->addParticipant($this->getReference("participant_" .rand(5, 9)));
//            $trip->setOrganizer($this->getReference("participant_" .rand(0, 9)));
//            $trip->setPlace($this->getReference("place_" .rand(0, 9)));
//            $this->addReference("trip_" . $i, $trip);
//            $manager->persist($trip);
//        }
// Evenement passé
        $cinema = new Trip();
        $cinema->setName("Cinéma : Gladiator 2");
        $startDate = new DateTime('2024-11-09 15:30:00');
        $registrationDeadline = new DateTime('2024-11-08 15:30:00');
        $cinema->setStartDateTime($startDate);
        $cinema->setDuration(150);
        $cinema->setRegistrationDeadline($registrationDeadline);
        $cinema->setRelativeCampus($this->getReference("campus_" . 1 ));
        $cinema->setNbRegistrationMax(15);
        $cinema->setInfo("Rendez vous au cinéma l'Arvor");
        $cinema->setState($this->getReference("state_" . 4));
        $cinema->setOrganizer($this->getReference("participant_" . rand(0, 9)));
        $cinema->setPlace($this->getReference("Cinéma l'Arvor"));
        $manager->persist($cinema);

//        Evenement a venir
        $poterie = new Trip();
        $poterie->setName("Atelier Poterie");
        $startDate = new DateTime('2024-12-10 10:30:00');
        $registrationDeadline = new DateTime('2024-12-05 18:30:00');
        $poterie->setStartDateTime($startDate);
        $poterie->setDuration(240);
        $poterie->setRegistrationDeadline($registrationDeadline);
        $poterie->setRelativeCampus($this->getReference("campus_" . 1 ));
        $poterie->setNbRegistrationMax(3);
        $poterie->setInfo("Atelier de poterie chez moi");
        $poterie->setState($this->getReference("state_" . 1));
        $poterie->setOrganizer($this->getReference("participant_" . rand(0, 9)));
        $poterie->setPlace($this->getReference('Poterie'));
        $manager->persist($poterie);

        //        Evenement a venir
        $durif = new Trip();
        $durif->setName("Conférence débat : Sylvain Durif");
        $startDate = new DateTime('2024-12-12 20:30:00');
        $registrationDeadline = new DateTime('2024-12-10 20:00:00');
        $durif->setStartDateTime($startDate);
        $durif->setRelativeCampus($this->getReference("campus_" . 1 ));
        $durif->setDuration(120);
        $durif->setRegistrationDeadline($registrationDeadline);
        $durif->setNbRegistrationMax(20);
        $durif->setInfo("Christ cosmique mythe ou réalité ?");
        $durif->setState($this->getReference("state_" . 1));
        $durif->setOrganizer($this->getReference("participant_" . rand(0, 9)));
        $durif->setPlace($this->getReference('TNB'));
        $manager->persist($durif);


        $sang = new Trip();
        $sang->setName("Journée don du sang");
        $startDate = new DateTime('2024-12-25 16:30:00');
        $registrationDeadline = new DateTime('2024-12-10 14:30:00');
        $sang->setStartDateTime($startDate);
        $sang->setDuration(15);
        $sang->setRelativeCampus($this->getReference("campus_" . 1 ));
        $sang->setRegistrationDeadline($registrationDeadline);
        $sang->setNbRegistrationMax(50);
        $sang->setInfo("Donnez votre sang c'est important");
        $sang->setState($this->getReference("state_" . 1));
        $sang->setOrganizer($this->getReference("participant_" . rand(0, 9)));
        $sang->setPlace($this->getReference('Dont Sang'));
        $manager->persist($sang);

        $plage = new Trip();
        $plage->setName("Dorer à Nice 🏖️");
        $startDate = new DateTime('2024-05-25 14:30:00');
        $registrationDeadline = new DateTime('2024-04-25 15:30:00');
        $plage->setStartDateTime($startDate);
        $plage->setDuration(15);
        $plage->setRelativeCampus($this->getReference("campus_" . 1 ));
        $plage->setRegistrationDeadline($registrationDeadline);
        $plage->setNbRegistrationMax(5);
        $plage->setInfo("Venez vous dorer au soleil avec moi, je vous emmene a Nice aller retour dans l'aprem tkt");
        $plage->setState($this->getReference("state_" . 1));
        $plage->setOrganizer($this->getReference("participant_11"));
        $plage->setPlace($this->getReference('Plage'));
        $manager->persist($plage);


//        Evenement annulé :


        $droite = new Trip();
        $droite->setName("Sensibilisation au droitardisme️");
        $startDate = new DateTime('2025-05-05 14:30:00');
        $registrationDeadline = new DateTime('2024-12-25 08:30:00');
        $droite->setStartDateTime($startDate);
        $droite->setDuration(60);
        $droite->setRelativeCampus($this->getReference("campus_" . 1 ));
        $droite->setRegistrationDeadline($registrationDeadline);
        $droite->setNbRegistrationMax(2);
        $droite->setInfo("J'ai besoin d'aide pour sensibliser nos force de l'ordre à ce fléau");
        $droite->setState($this->getReference("state_" . 5));
        $droite->setOrganizer($this->getReference("participant_" . rand(0, 9)));
        $droite->setPlace($this->getReference('Atelier'));
        $manager->persist($droite);



        $manager->flush();
    }

    public function getDependencies()
    {
        return [ParticipantFixtures::class, StateFixtures::class, CampusFixtures::class];
    }
}
