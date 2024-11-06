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


}