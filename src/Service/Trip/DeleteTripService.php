<?php


namespace App\Service\Trip;
;

use App\Entity\Trip;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;

class DeleteTripService
{
    private $entityManager;
    private $stateRepository;

    public function __construct(EntityManagerInterface $entityManager, StateRepository $stateRepository)
    {
        $this->entityManager = $entityManager;
        $this->stateRepository = $stateRepository;
    }

    public function deleteTrip(Trip $trip): void
    {
       $this->entityManager->remove($trip);
    }
}