<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de quelques villes d'exemple
        $cities = [
            ['name' => 'Paris', 'postalCode' => '75001'],
            ['name' => 'Marseille', 'postalCode' => '13001'],
            ['name' => 'Lyon', 'postalCode' => '69001'],
            ['name' => 'Toulouse', 'postalCode' => '31000'],
            ['name' => 'Nice', 'postalCode' => '06000'],
            ['name' => 'Nantes', 'postalCode' => '44000'],
            ['name' => 'Strasbourg', 'postalCode' => '67000'],
            ['name' => 'Montpellier', 'postalCode' => '34000'],
            ['name' => 'Bordeaux', 'postalCode' => '33000'],
            ['name' => 'Lille', 'postalCode' => '59000'],
            ['name'=> 'Rennes', 'postalCode' => '35000'],
        ];
        $i=0;
        foreach ($cities as $cityData) {
            $city = new City();
            $city->setName($cityData['name']);
            $city->setPostalCode($cityData['postalCode']);
            $this->addReference('city_' . $i, $city);

            $manager->persist($city);
            $i++;
        }

        // Enregistrer tous les objets en une seule fois
        $manager->flush();


    }
}
