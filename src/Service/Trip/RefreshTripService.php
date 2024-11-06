<?php

namespace App\Service\Trip;

use App\Entity\Trip;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;

class RefreshTripService
{

    private $entityManager;
    private $stateRepository;

    public function __construct(EntityManagerInterface $entityManager, StateRepository $stateRepository)
    {
        $this->entityManager = $entityManager;
        $this->stateRepository = $stateRepository;
    }


    function refreshTrip(array $trips){

        foreach($trips as $trip){
            $this->refreshDate($trip);
        }

    }




    private function refreshDate(Trip $trip)
    {
            $tripDateTime = $trip->getStartDateTime();
            $actualDateTime = new \DateTime('now');
            $diff = $actualDateTime->diff($tripDateTime);
            if($diff->invert){
                $stateWording = "Clôturée";
                $state = $this->stateRepository->findByWording($stateWording);
                $trip->setState($state);
                $this->entityManager->persist($trip);
                $this->entityManager->flush();
             }

    }


}