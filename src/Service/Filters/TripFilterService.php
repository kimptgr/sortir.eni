<?php

namespace App\Service\Filters;

use App\Repository\TripRepository;

class TripFilterService
{
    public function getTripWithFilters(TripRepository $tripRepository, mixed $filterChoices):array
    {
        return $tripRepository->findTripByFilters($filterChoices);
    }
}