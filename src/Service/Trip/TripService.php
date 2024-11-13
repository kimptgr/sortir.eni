<?php

namespace App\Service\Trip;

use App\Entity\Participant;
use App\Entity\State;
use App\Entity\Trip;
use App\Repository\StateRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TripService
{
    public function __construct(private EntityManagerInterface $entityManager, private StateRepository $stateRepository, private Security $security, private RefreshTripService $refreshTripService)
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
        if ($trip->getOrganizer() === $userInSession) {
            $flashMessage = ['warning', "Vous êtes le leader de l'event, on compte sur vous !"];
        } else if ($trip->getParticipants()->contains($userInSession)) {
            $flashMessage = ['warning', "Vous êtes déjà sur la liste des participants, pensez à l'enregistrer dans votre agenda ;)"];
        } else if ($trip->getParticipants()->count() >= $trip->getNbRegistrationMax()) {
            $flashMessage = ['error', 'Oups, évènement full !'];
        } else if ($trip->getState()->getWording() != 'Ouverte') {
            $flashMessage = ['error', 'Désolé les inscriptions ne sont pas ouvertes !'];
        } else if ($trip->getRegistrationDeadline() <= new DateTime()) {
            $flashMessage = ['error', 'Les inscriptions sont closes, fallait être plus rapide !'];
        } else {
            $trip->addParticipant($userInSession);
            $this->refreshTripService->checkNombreParticipant($trip);
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
            $flashMessage = ['success', 'Amusez-vous bien ' . $userInSession->getFirstName() . ' ! '];
        }

        return $flashMessage;

    }


    public function setTripState(Trip $trip, string $stateWording): array
    {
        $state = $this->stateRepository->findByWording($stateWording);

        if ($state) {
            $trip->setState($state);
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
        } else {
            throw new \Exception("State not found");
        }

        switch ($stateWording) {
            case STATE_CREATED:
                $flashMessage = ["success", "Évenement enregistré (invisible pour les autres utilisateurs)"];
                break;
            case STATE_OPEN:
                $flashMessage = ["success", "Évenement publié et ouvert aux inscriptions"];
                break;
            case STATE_CLOSED:
                $flashMessage = ["success", "Clôture des inscriptions"];
                break;
            case STATE_ACTIVITY_IN_PROGRESS:
                $flashMessage = ["success", "Activité en cours"];
                break;
            case STATE_ACTIVITY_PAST:
                $flashMessage = ["success", "Activité passée"];
                break;
            case STATE_ACTIVITY_CANCELED:
                $flashMessage = ["success", "Sortie annulée"];
                break;
            case STATE_HISTORICIZED:
                $flashMessage = ["success", "Sortie historisée"];
                break;
        }

        return $flashMessage;
    }

    public function deleteTrip(Trip $trip): void
    {
        $this->entityManager->remove($trip);
    }

    public function removeAParticipant(Trip $trip)
    {
        $security = $this->security;
        $userInSession = $security->getUser();
        $flashMessage = [];
        if (count($trip->getParticipants()) === $trip->getNbRegistrationMax()
            && $trip->getRegistrationDeadline() > getDate()
            && ($trip->getState()->getWording() !== STATE_ACTIVITY_CANCELED || $trip->getState()->getWording() !== STATE_ACTIVITY_PAST ))  {
            $this->setTripState($trip, STATE_OPEN);
        }

        $trip->removeParticipant($userInSession);
        $this->entityManager->persist($trip);
        $this->entityManager->flush();
        $flashMessage = ["success", "Désinscription effectuée"];

        return $flashMessage;
    }

    public function cancelTrip(Trip $trip, string $reason)
    {
        $flashMessage = [];
        if ($trip->getState()->getWording() != STATE_ACTIVITY_IN_PROGRESS) {
            $newDescription = $trip->getInfo() . '[MOTIF D\'ANNULATION : ' . $reason . ']';
            $trip->setInfo($newDescription);
            $flashMessage = $this->setTripState($trip, STATE_ACTIVITY_CANCELED);
        } else {
            $flashMessage = ['error', 'Une activité en cours ne peut être annulée'];
        }

        return $flashMessage;
    }

}