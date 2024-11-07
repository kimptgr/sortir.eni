<?php

namespace App\Service\Filters;

use App\Entity\Participant;
use App\Form\TripFilterType;
use App\Models\TripFilterModel;
use App\Repository\TripRepository;
use Symfony\Bundle\SecurityBundle\Security;

class TripFilterService
{
    public function __construct(private TripRepository $tripRepository, private Security $security)
    {
        $this->tripRepository = $tripRepository;
        $this->security = $security;

    }

    public function getTripWithFilters(TripFilterModel $filterChoices):array
    {
        $userInSession = $this->security->getUser();
        return $this->tripRepository->findTripByFilters($filterChoices, $userInSession);
    }

}