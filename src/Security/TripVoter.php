<?php


namespace App\Security;

use App\Entity\Participant;
use App\Entity\Trip;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


/**
 *Classe en charge de centralisée la logique de perimission afin qu'elle puisse être réutilisée à différents endroits
 */
class TripVoter extends Voter{

//Constantes nommées comme on le souhaite, en fonction des actions dans le code pour lesquelles il faudra vérifier la permission
    const EDIT = 'EDIT';
    const DELETE = 'DELETE';
    const PUBLISH = 'PUBLISH';
    const CANCEL = 'CANCEL';
    const DESIST = 'DESIST';
    const PARTICIPATE = 'PARTICIPATE';

    /**
     * Méthode hérités : supports() en charge de vérifier sir le voter doit s'appliquer en fonction de l'action et du sujet
     * @param string $attribute action demandée (les actions définies dans les constantes)
     * @param $subject  = Objet sur lequel l'action est demandée
     * @return bool retourne true si le voter doit s'appliquer
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        //Liste les actions auxquelles nous nous intéressons sur un Trip
        if (!in_array($attribute, [self::EDIT, self::DELETE, self::PUBLISH, self::CANCEL, self::DESIST, self::PARTICIPATE])) {
            return false;
        }

        // Vérifie que le "subject" est une instance de Trip
        if (!$subject instanceof Trip) {
            return false;
        }

        return true;
    }

    /**
     * Méthode en charge de déterminer si l'utilisateur peut réaliser l'action
     * @param string $attribute = action
     * @param $subject = objet de l'action
     * @param TokenInterface $token jeton de sécu qui donne utilisateur
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur n'est pas connecté, il ne peut pas effectuer l'action
        if (!$user instanceof Participant) {
            return false;
        }

        //Assurance que le sujet est bien de type Trip grâce à supports()
        /** @var Trip $trip */
        $trip = $subject;

        // Découpage des responsabilités en donnant la vérification à +rs autres fonctions
        return match ($attribute) {
            self::EDIT => $this->canEdit($trip, $user),
            self::DELETE => $this->canDelete($trip, $user),
            self::PUBLISH => $this->canPublish($trip, $user),
            self::CANCEL => $this->canCancel($trip, $user),
            self::DESIST => $this->canDesist($trip, $user),
            self::PARTICIPATE => $this->canParticipate($trip, $user),
            default => false
        };
    }

    private function canEdit(Trip $trip, Participant $participant): bool {
            return in_array('ROLE_ADMIN', $participant->getRoles()) || $participant === $trip->getOrganizer();
    }

    private function canDelete(Trip $trip, Participant $participant): bool {
        return in_array('ROLE_ADMIN', $participant->getRoles()) || $participant === $trip->getOrganizer();
    }

    private function canPublish(Trip $trip, Participant $participant): bool {
        return $participant === $trip->getOrganizer() && $trip->getState()->getWording() === STATE_CREATED;
    }

    private function canCancel(Trip $trip, Participant $participant): bool {
       return in_array('ROLE_ADMIN', $participant->getRoles())
           ||$participant === $trip->getOrganizer()
            && ($trip->getState()->getWording() == STATE_CREATED
                || $trip->getState()->getWording() == STATE_OPEN
                || $trip->getState()->getWording() == STATE_CLOSED
            );
    }

    private function canDesist(Trip $trip, Participant $participant): bool {
        $participants = $trip->getParticipants();
         return $participant !== $trip->getOrganizer()
            && $participants->contains($participant)
            && ( $trip->getState()->getWording() !== STATE_ACTIVITY_IN_PROGRESS
                || $trip->getState()->getWording() !== STATE_ACTIVITY_PAST
            );
    }

    private function canParticipate(Trip $trip, Participant $participant): bool {
        $participants = $trip->getParticipants();
        return $participant !== $trip->getOrganizer()
            && !$participants->contains($participant)
            && $trip->getState()->getWording() == STATE_OPEN ;
    }
}