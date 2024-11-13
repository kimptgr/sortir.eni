<?php

namespace App\Service\Trip;

use App\Entity\Trip;
use App\Repository\StateRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;

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

    public function refreshTrip()
    {
        $trips = $this->tripRepository->findTripRefresh();
        $states = $this->stateRepository->findAllState();
        foreach ($trips as $trip) {
            $this->refreshStartDate($trip, $states);
            $this->entityManager->persist($trip);
        }
        $this->entityManager->flush();
    }

    private function refreshStartDate(Trip $trip, $states): void
    {
        $tripStartDateTime = $trip->getStartDateTime();
        $tripEndDateTime = clone $tripStartDateTime;
        $tripEndDateTime->modify('+' . $trip->getDuration() . ' minutes');
        $tripRegistrationDeadLine = $trip->getRegistrationDeadLine();
        $actualDateTime = new \DateTime('now');

        //Le diff ici crée un dateInterval à partir de la différence entre  $actualDateTime et $tripEndDateTime
        //Negatif si $actualDateTime est antérieur à $tripEndDateTime.
        $diffEndDateTime = $actualDateTime->diff($tripEndDateTime);

        // Check si archivé -> historisé
        // Le diff() permet de retourner un dateInterval
        // Ici on regarde si le moi est >= a 1 et l'année > 0
        if (($diffEndDateTime->m >= 1 || $diffEndDateTime->y > 0) && $trip->getState()->getWording() !== STATE_HISTORICIZED) {
            $trip->setState($this->findStateByWording($states, STATE_HISTORICIZED));
        }

        // Test si activité a dépassé la date d'inscription
        if ($tripRegistrationDeadLine < $actualDateTime) {
            $trip->setState($this->findStateByWording($states, STATE_CLOSED));
        }

        //        Test si activité en cour
        // Ici diffEnDateTime  si le date est avant
        if ($tripEndDateTime > $actualDateTime && $tripStartDateTime <= $actualDateTime) {
            $trip->setState($this->findStateByWording($states, STATE_ACTIVITY_IN_PROGRESS));
        }

        // Check si activité est terminée
        if ($actualDateTime>$tripEndDateTime) {
            $trip->setState($this->findStateByWording($states, STATE_ACTIVITY_PAST));
        }

        $this->entityManager->persist($trip);

    }

    private function findStateByWording($states, $wording)
    {
        foreach ($states as $state) {
            if ($state->getWording() === $wording) {
                return $state;
            }
        }
        throw new \Exception("Erreur : état '$wording' non trouvé");
    }

    public function checkNombreParticipant(Trip $trip)
    {
        $nombreParticipant = $trip->getNbRegistrationMax();
        $count = count($trip->getParticipants());
        $states = $this->stateRepository->findAll();


        if ($count >= $nombreParticipant) {
            $trip->setState($this->findStateByWording($states,STATE_CLOSED));
            $this->entityManager->persist($trip);
        } else {
            $trip->setState($this->findStateByWording($states,STATE_OPEN));
            $this->entityManager->persist($trip);
        }
        $this->entityManager->flush();
    }
}
