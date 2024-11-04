<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer plusieurs campus avec des noms
        $campusNames = [
            'Campus St-Herblain ( Nantes )',
            'Campus Chartre-de-Bretagne ( Rennes )',
            'Campus Quimper',
            'Campus Niort',
            'Campus en ligne',
        ];

        foreach ($campusNames as $name) {
            $campus = new Campus();
            $campus->setName($name);


            // Persist the Campus object
            $manager->persist($campus);
        }

        // Flush pour enregistrer les changements dans la base de données
        $manager->flush();
    }
}
