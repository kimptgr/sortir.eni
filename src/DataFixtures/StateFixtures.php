<?php

namespace App\DataFixtures;

use App\Entity\State;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StateFixtures extends Fixture
{
    public function load(ObjectManager $manager): void{


        $states = [
            'Créée',
            'Ouverte',
            'Clôturée',
            'Activité en cours',
            'passée',
            "Annulée",
        ];
        $i=0;
        foreach ($states as $etat) {
            $state= new State();
            $state->setWording($etat);
            $this->addReference('state_'.$i, $state);
            $i++;
        }

        $manager->flush();
    }
}
