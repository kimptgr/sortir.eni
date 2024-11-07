<?php

namespace App\Service\Trip;

use App\Entity\Participant;
use App\Entity\State;
use App\Entity\Trip;
use App\Repository\StateRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class TripService
{
   public function __construct(private EntityManagerInterface $entityManager, private StateRepository $stateRepository, private Security $security)
    {
        $this->entityManager = $entityManager;
        $this->stateRepository = $stateRepository;
        $this->security = $security;
    }


    public function addAParticipant(Trip $trip)
    {
        $security = $this->security;
        $userInSession = $security->getUser();

        $flashMessage = [];
        if ($trip->getOrganizer() === $userInSession){
            $flashMessage = ['warning', "Vous êtes le leader de l'event, on compte sur vous !"];
        }
        else if ($trip->getParticipants()->contains($userInSession)) {
            $flashMessage = ['warning', "Vous êtes déjà sur la liste des participants, pensez à l'enregistrer dans votre agenda ;)"];
        }
        else if ($trip->getParticipants()->count() >= $trip->getNbRegistrationMax()) {
            $flashMessage = ['error', 'Oups, évènement full !'];

        }
        else if ($trip->getState()->getWording() != 'Ouverte'){
            $flashMessage = ['error', 'Désolé les inscriptions ne sont pas ouvertes !'];
        }
        else if ($trip->getRegistrationDeadline() > new DateTime() ){
            $flashMessage = ['error', 'Les inscriptions sont closes, fallait être plus rapide !'];
        }
        else {
            $trip->addParticipant($userInSession);
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
            $flashMessage = ['success', 'Amusez-vous bien ' . $userInSession->getFirstName() . ' ! '];
        }

        return $flashMessage;

    }


    public function setTripState(Trip $trip, string $stateWording): array
    {
        $state= $this->stateRepository->findByWording($stateWording);

        if ($state) {
            $trip->setState($state);
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
        } else {
            throw new \Exception("State not found");
        }

        switch($stateWording){
            case 'Créée':
                $flashMessage= ["success","enregistré"];
            case 'Ouverte':
                $flashMessage= ["success", "publié"];
        }

        return $flashMessage ;
    }

    public function deleteTrip(Trip $trip): void
    {
        $this->entityManager->remove($trip);
    }


}