<?php

namespace App\Service\Filters;

use App\Entity\Participant;
use App\Repository\TripRepository;

class TripFilterService
{
    private $tripRepository;
    public function __construct(TripRepository $tripRepository)
    {
        $this->tripRepository = $tripRepository;
    }

    public function getTripWithFilters(mixed $filterChoices, Participant $userInSession):array
    {
        return $this->tripRepository->findTripByFilters($filterChoices, $userInSession);
    }
}