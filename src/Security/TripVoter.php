<?php


namespace App\Security;

use App\Entity\Trip;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class TripVoter extends Voter{


    const EDIT = 'EDIT';
    const DELETE = 'DELETE';


    protected function supports(string $attribute, $subject): bool
    {
        // Nous nous intéressons ici uniquement aux actions "EDIT" et "DELETE" sur un Article
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        // Vérifie que le "subject" est une instance d'Article
        if (!$subject instanceof Trip) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();



        // Si l'utilisateur n'est pas connecté, il ne peut pas effectuer l'action
        if (!$user) {
            return false;
        }

        /** @var Trip $article */
        $trip = $subject;

        // LA VERIF
        // Si l'utilisateur est l'auteur de la sortie, il peut modifier ou supprimer
        if ($user === $trip->getOrganizer()) {
            return true;
        }

        // Par défaut, l'accès est refusé
        return false;
    }
}