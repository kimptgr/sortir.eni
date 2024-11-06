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
            'Activité passée',
            'Activité annulée'
        ];
        $i=0;
        foreach ($states as $wording) {
            $state = new State();
            $state->setWording($wording);
            $this->addReference('state_' . $i, $state);
            $manager->persist($state);
            $i++;
        }
        $manager->flush();
    }
}
