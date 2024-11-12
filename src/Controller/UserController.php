<?php

namespace App\Controller;

use App\Form\ParticipantFilterType;
use App\Models\ParticipantFilterModel;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();




        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

    }


    #[Route('/participants', name: 'participant_list')]
    #[isGranted('ROLE_ADMIN')]
    public function list(Request $request, ParticipantRepository $participantRepository): Response
    {
        $filterModel = new ParticipantFilterModel();
        $form = $this->createForm(ParticipantFilterType::class, $filterModel);
        $form->handleRequest($request);

        $participants = $participantRepository->findParticipantsByFilters($filterModel);




        return $this->render('registration/listUser.html.twig', [
            'form' => $form->createView(),
            'participants' => $participants,
        ]);
    }

    #[Route('/delete-participants', name: 'delete_participants', methods: ['POST'])]
    #[isGranted('ROLE_ADMIN')]
    public function deleteParticipants(Request $request, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['ids'] ?? [];

        if (!is_array($ids)) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        foreach ($ids as $id) {
            $participant = $participantRepository->find($id);
            if ($participant) {
                $entityManager->remove($participant);
            }
        }

        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }


    #[Route('/active-participants/{id}', name: 'active_participants')]
    #[isGranted('ROLE_ADMIN')]
    public function activParticipants(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le participant par son ID
        $participant = $participantRepository->find($id);

        if (!$participant) {
            throw $this->createNotFoundException('Participant introuvable.');
        }

        // Changer l'état de l'activation
        if ($participant->isActive()) {
            $participant->setActive(false);
        } else {
            $participant->setActive(true);
        }

        // Enregistrer les modifications dans la base de données
        $entityManager->persist($participant);
        $entityManager->flush();

        // Redirection vers la liste des participants
        return $this->redirectToRoute('participant_list');
    }





}
