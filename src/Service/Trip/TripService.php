<?php

namespace App\Service\Trip;

use App\Entity\Participant;
use App\Entity\Trip;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;

class TripService
{


    private $entityManager;
    private $stateRepository;

    public function __construct(EntityManagerInterface $entityManager, StateRepository $stateRepository)
    {
        $this->entityManager = $entityManager;
        $this->stateRepository = $stateRepository;
    }


    public function addAParticipant(Participant $userInSession)
    {


    }


    public function setTripState(Trip $trip, string $stateWording): void
    {
        $state = $this->stateRepository->findOneBy(['wording' => $stateWording]);
        if ($state) {
            $trip->setState($state);
        } else {
            throw new \Exception("State not found");
        }
    }

    public function deleteTrip(Trip $trip): void
    {
        $this->entityManager->remove($trip);
    }


}