<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantFormType;
use App\Form\RegistrationFormType;
use App\Service\File\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader
    ): Response {
        // Créer un nouvel utilisateur
        $user = new Participant();

        // Créer le formulaire pour l'enregistrement
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hashage du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Traitement du fichier uploadé
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();

            if ($brochureFile) {
                // Utiliser le service FileUploader pour gérer l'upload
                $brochureFileName = $fileUploader->upload($brochureFile);
                $user->setBrochureFilename($brochureFileName); // Sauvegarder le nom du fichier dans la base de données
            }

            // Assigner des valeurs par défaut à l'utilisateur
            $user->setRoles(['ROLE_USER']);
            $user->setActive(true);

            // Enregistrer l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection après inscription
            return $this->redirectToRoute('app_login');
        }

        // Rendu du formulaire
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


    // =========================================================================================================
    // =========================================================================================================


    #[Route('/edit', name: 'app_edit_profile')]
    public function editProfile(Request $request,
                                EntityManagerInterface $entityManager,
                                UserPasswordHasherInterface $userPasswordHasher,
                                FileUploader $fileUploader
    ): Response {




        // Récupérer l'utilisateur connecté
        $user = $this->getUser();



        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        dump($user);

        // Créer et remplir le formulaire avec les données de l'utilisateur
        $form = $this->createForm(ParticipantFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            dump('submit se lance');

            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData(); // est null



            dump($brochureFile);

            if ($brochureFile) {
                // Utiliser le service FileUploader pour gérer l'upload
                $oldFilename = $user->getBrochureFilename(); // on chope l'ancien repertoire
                dump($oldFilename);
                $brochureFileName = $fileUploader->upload($brochureFile); // on cree le nouveau et on met le fichier dans public/uploads/...
                dump($brochureFileName);
                $user->setBrochureFilename($brochureFileName); // Sauvegarder le nom du fichier dans la base de données
                dump($user->getBrochureFilename());
                $fileUploader->delete($oldFilename); // on supprime l'ancien repertoire parce qu'inutile
                dump($oldFilename);

            }


            $newPassword = $form->get('password')->getData();
            $oldPassword = $user->getPassword();




            if($newPassword == $oldPassword){
                $this->addFlash('alert','Veuillez rentrer un nouveau mot de passe');
            }
            elseif($newPassword !== null && $newPassword !== $oldPassword){
                $user->setPassword($newPassword);
            }








            $entityManager->persist($user);

            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();

            // Ajouter un message flash pour indiquer le succès de l'opération
            $this->addFlash('success', 'Profil mis à jour avec succès.');

            // Rediriger l'utilisateur vers la page de son profil
            return $this->redirectToRoute('app_profile');
        }

        // Rendu de la vue avec le formulaire
        return $this->render('registration/editProfile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }




}

