<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer plusieurs campus avec des noms
        $campusNames = [
            'Campus St-Herblain (Nantes)',
            'Campus Chartre-de-Bretagne (Rennes)',
            'Campus Quimper',
            'Campus Niort',
            'Campus en ligne',
        ];

        $i=0;
        foreach ($campusNames as $name) {
            $campus = new Campus();
            $campus->setName($name);
            $manager->persist($campus);
            $this->addReference('campus_'. $i, $campus);
            $i++;
        }

        $manager->flush();

    }


}
