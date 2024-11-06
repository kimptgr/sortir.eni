<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));




            $user->setRoles(['ROLE_USER']);
            $user->setActive(true);


            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }



    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response{


        $user = $this->getUser();


        if (!$user) {
            return $this->redirectToRoute('app_login');
        }


        return $this->render('registration/profile.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/edit', name: 'app_edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        dump($user); // DEVTEST

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Créer et remplir le formulaire avec les données de l'utilisateur
        $form = $this->createForm(ParticipantFormType::class, $user);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on met à jour l'utilisateur
        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour les informations de l'utilisateur en BDD
            $entityManager->flush();

            // Rediriger l'utilisateur vers la page de profil après la mise à jour
            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('app_profile');
        }

        // Rendu de la vue avec le formulaire
        return $this->render('registration/editProfile.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
