<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');



        $participantAdmin = new Participant();
        $participantAdmin->setFirstName('admin');
        $participantAdmin->setLastName('admin');
        $participantAdmin->setEmail('admin@admin.com');
        $participantAdmin->setPassword($this->passwordHasher->hashPassword($participantAdmin, 'password'));
        $participantAdmin->setActive(1);
        $participantAdmin->setPhoneNumber($faker->phoneNumber);
        $randomCampusAdmin = $this->getReference("campus_".rand(0,4));
        $participantAdmin->setCampus($randomCampusAdmin);

        $participantAdmin->setRoles(['ROLE_USER','ROLE_ADMIN']);

        $this->addReference("participant_admin", $participantAdmin);

        $manager->persist($participantAdmin);

        $participant = new Participant();
        $participant->setFirstName('Marie');
        $participant->setLastName('Tartine');
        $participant->setEmail('mtartine@dej.com');
        $participant->setPhoneNumber($faker->phoneNumber);
        $participant->setActive(1);
        $randomCampus = $this->getReference("campus_".rand(0,4));
        $participant->setCampus($randomCampus);
        $password = $this->passwordHasher->hashPassword($participant, 'aaaaa1');
        $participant->setPassword($password);
        $participant->setRoles(['ROLE_USER']);
        $this->addReference("participant_11", $participant);
        $manager->persist($participant);

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

    public function getDependencies()
    {
        return [PlaceFixtures::class];
    }
}
