<?php

namespace App\Service\Trip;

use App\Entity\Trip;
use App\Repository\StateRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshTripService
{

    private $entityManager;
    private $stateRepository;

    private $tripRepository;

    public function __construct(EntityManagerInterface $entityManager, StateRepository $stateRepository, TripRepository $tripRepository)
    {
        $this->entityManager = $entityManager;
        $this->stateRepository = $stateRepository;
        $this->tripRepository = $tripRepository;
    }


    function refreshTrip(){

        $trips= $this->tripRepository->findAll();

        foreach($trips as $trip){
            $this->refreshDate($trip);
            $this->checkNombreParticipant($trip);
        }

    }


    private function checkNombreParticipant(Trip $trip)
    {

        $nombreParticipant = $trip->getNbRegistrationMax();
        $count = count($trip->getParticipants());


        if($count >= $nombreParticipant){

            $state = $this->stateRepository->findByWording('Clôturée');
            $trip->setState($state);
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
        }else{

            $state = $this->stateRepository->findOneBy(['wording' => "Ouverte"]);
            $trip->setState($state);
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
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