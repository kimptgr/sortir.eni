<?php

namespace App\DataFixtures;

use App\Entity\Place;
use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
//        for ($i = 0; $i < 10; $i++) {
//            $place = new Place();
//            $place->setCity($this->getReference('city_'. rand(0,9)));
//            $place->setName($faker->word());
//            $place->setStreet($faker->streetName());
//            $place->setLatitude($faker->latitude());
//            $place->setLongitude($faker->longitude());
//            $this->addReference('place_'. $i, $place);
//            $manager->persist($place);
//        }

        $placeArvor = new Place();
        $placeArvor->setCity($this->getReference('city_' . 10));
        $placeArvor->setName("Cinéma l'Arvor");
        $placeArvor->setStreet('11 Rue de Châtillon');
        $placeArvor->setLatitude($faker->latitude());
        $placeArvor->setLongitude($faker->longitude());
        $this->addReference("Cinéma l'Arvor", $placeArvor);
        $manager->persist($placeArvor);


        $placePoterie = new Place();
        $placePoterie->setCity($this->getReference('city_' . 10));
        $placePoterie->setName("Métro la Poterie");
        $placePoterie->setStreet("Rue Émile Litré");
        $placePoterie->setLatitude($faker->latitude());
        $placePoterie->setLongitude($faker->longitude());
        $this->addReference('Poterie', $placePoterie);
        $manager->persist($placePoterie);

        $placePlage = new Place();
        $placePlage->setCity($this->getReference('city_' . 4));
        $placePlage->setName("La Plage");
        $placePlage->setStreet("rue de la plage");
        $placePlage->setLatitude($faker->latitude());
        $placePlage->setLongitude($faker->longitude());
        $this->addReference('Plage', $placePlage);
        $manager->persist($placePlage);

        $placeTNB = new Place();
        $placeTNB->setCity($this->getReference('city_' . 10));
        $placeTNB->setName("TNB");
        $placeTNB->setStreet("1 rue Saint-Helier");
        $placeTNB->setLatitude($faker->latitude());
        $placeTNB->setLongitude($faker->longitude());
        $this->addReference('TNB', $placeTNB);
        $manager->persist($placeTNB);

        $placeDontSang = new Place();
        $placeDontSang->setCity($this->getReference('city_' . 10));
        $placeDontSang->setName("Dont Sang");
        $placeDontSang->setStreet("Rue Vasselot");
        $placeDontSang->setLatitude($faker->latitude());
        $placeDontSang->setLongitude($faker->longitude());
        $this->addReference('Dont Sang', $placeDontSang);
        $manager->persist($placeDontSang);

        $placeAtelierDroite = new Place();
        $placeAtelierDroite->setCity($this->getReference('city_' . 10));
        $placeAtelierDroite->setName("Atelier");
        $placeAtelierDroite->setStreet("22 Bd de la Tour d'Auvergne");
        $placeAtelierDroite->setLatitude($faker->latitude());
        $placeAtelierDroite->setLongitude($faker->longitude());
        $this->addReference('Atelier', $placeAtelierDroite);
        $manager->persist($placeAtelierDroite);


        $manager->flush();
    }
    public function getDependencies()
    {
        return [CityFixtures::class];
    }
}
