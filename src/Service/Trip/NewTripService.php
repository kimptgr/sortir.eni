<?php

namespace App\Service\Trip;
;

use App\Entity\Trip;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;

class NewTripService
{
    private $entityManager;
    private $stateRepository;

    public function __construct(EntityManagerInterface $entityManager, StateRepository $stateRepository)
    {
        $this->entityManager = $entityManager;
        $this->stateRepository = $stateRepository;
    }

    public function createTripWithState(Trip $trip, string $stateWording): void
    {
        $state = $this->stateRepository->findOneBy(['wording' => $stateWording]);
        if ($state) {
            $trip->setState($state);
        } else {
            throw new \Exception("State not found");
        }
    }
}