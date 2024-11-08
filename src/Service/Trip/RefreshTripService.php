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
        $states = $this->stateRepository->findAll();
        foreach ($trips as $trip) {
            $this->refreshStartDate($trip, $states);
            $this->entityManager->persist($trip);
        }
        $this->entityManager->flush();
    }

    private function refreshStartDate(Trip $trip, $states): void
    {
        $tripDateTime = $trip->getStartDateTime();
        $tripEndDateTime = clone $tripDateTime;
        $tripRegistrationDeadLine = $trip->getRegistrationDeadLine();
        $tripEndDateTime->modify('+' . $trip->getDuration() . ' minutes');
        $actualDateTime = new \DateTime('now');

        // Check si archivé -> historisé
        $diff = $actualDateTime->diff($tripEndDateTime);
        if (($diff->m >= 1 || $diff->y > 0) && $trip->getState()->getWording() !== 'Historisée') {
            $trip->setState($this->findStateByWording($states, "Historisée"));
            $this->entityManager->persist($trip);
        }

        // Check si activité est terminée
        $diff = $actualDateTime->diff($tripEndDateTime);
        if ($diff->invert) {
            $trip->setState($this->findStateByWording($states, "Activité passée"));
            $this->entityManager->persist($trip);
        }

//        Test si activité en cour
        $diff = $actualDateTime->diff($tripEndDateTime);
        $diff2 = $actualDateTime->diff($tripDateTime);
        if (!$diff->invert && $diff2->invert) {
            $trip->setState($this->findStateByWording($states, "Activité en cours"));
            $this->entityManager->persist($trip);
        }

//        Test si activité a dépassé la date d'inscription
        $diff = $actualDateTime->diff($tripRegistrationDeadLine);
        if ($diff->invert) {
            $trip->setState($this->findStateByWording($states, "Clôturée"));
            $this->entityManager->persist($trip);
        }
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
            $trip->setState($this->findStateByWording($states,'Clôturée'));
            $this->entityManager->persist($trip);
        } else {
            $trip->setState($this->findStateByWording($states,'Ouverte'));
            $this->entityManager->persist($trip);
        }
        $this->entityManager->flush();
    }
}
