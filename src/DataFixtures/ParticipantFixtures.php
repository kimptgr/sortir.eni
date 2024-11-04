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


        $campuses = [];
        $campusNames = [
            'Campus St-Herblain ( Nantes )',
            'Campus Chartre-de-Bretagne ( Rennes )',
            'Campus Quimper',
            'Campus Niort',
            'Campus en ligne'
        ];

        foreach ($campusNames as $campusName) {
            $campus = new Campus();
            $campus->setName($campusName);
            $manager->persist($campus);
            $campuses[] = $campus;
        }


        for ($i = 0; $i < 10; $i++) {
            $participant = new Participant();
            $participant->setFirstName($faker->firstName);
            $participant->setLastName($faker->lastName);
            $participant->setEmail($faker->unique()->email);
            $participant->setPhoneNumber($faker->phoneNumber);
            $participant->IsActive($faker->boolean);


            $randomCampus = $faker->randomElement($campuses);
            $participant->setCampus($randomCampus);


            $password = $this->passwordHasher->hashPassword($participant, 'password123');
            $participant->setPassword($password);

            $participant->setRoles(['ROLE_USER']);

            $manager->persist($participant);
        }


        $manager->flush();
    }
}
