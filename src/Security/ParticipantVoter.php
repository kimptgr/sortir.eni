<?php


namespace App\Security;

use App\Entity\Participant;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


/**
 *Classe en charge de centralisée la logique de perimission afin qu'elle puisse être réutilisée à différents endroits
 */
class ParticipantVoter extends Voter{
        const EDIT = 'EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Participant) {
            return false;
        }

        return true;

    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $userInSession = $token->getUser();
        if (!$userInSession instanceof Participant) {
            return false;
        }

        /** @var  Participant $participant */
        $participant = $subject;

        return match($attribute) {
            self::EDIT => $this->canEdit($participant, $userInSession),
            default => false,
        };


    }

    private function canEdit(Participant $participant, Participant $userInSession):bool
    {
        return in_array('ROLE_ADMIN', $userInSession->getRoles()) || $userInSession === $participant;
    }
}
