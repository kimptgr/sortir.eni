<?php

namespace App\DataFixtures;

use App\Entity\State;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StateFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $states = [
            'Créée',
            'Ouverte',
            'Clôturée',
            'Activité en cours',
            'Activité passé',
            'Activité annulée'
        ];

        foreach ($states as $wording) {
            $state = new State();
            $state->setWording($wording);


            // Persist the Campus object
            $manager->persist($state);
        }

        // Save data in db
        $manager->flush();
    }
}
