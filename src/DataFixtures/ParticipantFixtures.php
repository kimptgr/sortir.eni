<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');




        for ($i = 0; $i < 10; $i++) {
            $participant = new Participant();
            $participant->setFirstName($faker->firstName);
            $participant->setLastName($faker->lastName);
            $participant->setEmail($faker->unique()->email);
            $participant->setPhoneNumber($faker->phoneNumber);
            $participant->setActive($faker->numberBetween(0, 1));


            $randomCampus = $this->getReference("campus_".rand(0,4));
            $participant->setCampus($randomCampus);


            $password = $this->passwordHasher->hashPassword($participant, 'password123');
            $participant->setPassword($password);

            $participant->setRoles(['ROLE_USER']);
            $this->addReference("participant_".$i, $participant);
            $manager->persist($participant);

        }


        $manager->flush();
    }
}
