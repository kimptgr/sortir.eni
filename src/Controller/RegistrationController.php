<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantFormType;
use App\Form\ParticipantFormTypePassword;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use App\Service\File\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as SensioSecurity;


class RegistrationController extends AbstractController
{


    public function __construct(private MailerInterface $mailer)
    {

    }





    #[Route('/register', name: 'app_register')]
    #[isGranted('ROLE_ADMIN')]
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


    // ---------------------------------------------------------------------------------------------------------------------


    #[Route('/profile/{pseudo?}', name: 'app_profile')]
    public function profile(Security $security, LogoutUrlGenerator $logoutUrlGenerator, ParticipantRepository $repository, ?string $pseudo=null): Response
    {
        if ($pseudo === null){
            $participant = $this->getUser();
        }else {
            $participant = $repository->findOneBy(['pseudo' => $pseudo]);
        }

        if ($participant === $this->getUser() && !$participant->isActive()) {
            $this->addFlash('danger', 'Votre compte est désactivé. Veuillez contacter l\'administration.');

            // Déconnecter l'utilisateur en générant l'URL de déconnexion
            $logoutUrl = $logoutUrlGenerator->getLogoutUrl('main'); // 'main' > nom du proxy => on a besoin d'une url securisée sinon le token est invalidé
            return new RedirectResponse($logoutUrl);
        }

        return $this->render('registration/profile.html.twig', [
            'user' => $participant,
        ]);
    }


    // =========================================================================================================
    // =========================================================================================================


    #[Route('/edit/{pseudo?}', name: 'app_edit_profile')]
    #[SensioSecurity("is_granted('EDIT', participant)")]
    public function editProfile(Request $request,
                                EntityManagerInterface $entityManager,
                                FileUploader $fileUploader,
                                ParticipantRepository $repository,
                                ?string $pseudo=null
    ): Response {

        if ($pseudo === null){
            $user = $this->getUser();
        }else {
            $user = $repository->findOneBy(['pseudo' => $pseudo]);
        }



        // Créer et remplir le formulaire avec les données de l'utilisateur
        $form = $this->createForm(ParticipantFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData(); // est null


            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];


            if ($brochureFile && in_array($brochureFile->getMimeType(), $allowedMimeTypes)) {
                // Utiliser le service FileUploader pour gérer l'upload
                $oldFilename = $user->getBrochureFilename(); // on chope l'ancien repertoire

                $brochureFileName = $fileUploader->upload($brochureFile); // on cree le nouveau et on met le fichier dans public/uploads/...

                $user->setBrochureFilename($brochureFileName); // Sauvegarder le nom du fichier dans la base de données

                $fileUploader->delete($oldFilename); // on supprime l'ancien repertoire parce qu'inutile

            }
            else{
                throw new \Exception('Type de fichier non autorisé ou inexistant');
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


//-----------------------------------------------------------------------------------------------


#[Route('/passwordModifier', name: 'password_modifier')]
#[isGranted('ROLE_USER')]
public function changerPassword(Request $request,UserPasswordHasherInterface $userPasswordHasher,
                                EntityManagerInterface $entityManager,
): Response
{
   $user = $this->getUser();

   $form= $this->createForm(ParticipantFormTypePassword::class, $user);

   $form->handleRequest($request);

   if (!$user) {
       return $this->redirectToRoute('app_login');
   }


    if($form->isSubmitted() && $form->isValid()){


        // Récupérer le mot de passe en clair (depuis le formulaire)
        $newPassword = $form->get('password')->getData(); // MDP form

        // Récupérer l'ancien mot de passe haché (depuis l'utilisateur)
        $oldPassword = $user->getPassword(); // mot de passe haché de l'utilisateur ( BDD )

        // Vérifier si le nouveau mot de passe est le même que l'ancien // ??? > pas le bon commentaire ?
        if ($newPassword && $userPasswordHasher->isPasswordValid($user, $newPassword)) {
            $this->addFlash('alert', 'Veuillez entrer un nouveau mot de passe.');
        } else {
            // Si un mot de passe a été fourni et qu'il est différent, on le hache et on le met à jour
            if ($newPassword) {
                $hashedPassword = $userPasswordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }
        }


        $entityManager->persist($user);

        // Sauvegarder les modifications dans la base de données
        $entityManager->flush();

        // Ajouter un message flash pour indiquer le succès de l'opération
        $this->addFlash('success', 'Mot de passe mis à jour avec succès.');

        // Rediriger l'utilisateur vers la page de son profil
        return $this->redirectToRoute('app_profile');
    }


   return $this->render('registration/passwordModifier.html.twig', [
       'user' => $user,
       'form' => $form->createView()
   ]);

}




// =============================================== FONCTIONS =======================================================


    public function sendEmail(): Response
    {
        $email = (new Email())
            ->from('sender@example.com')
            ->to('recipient@example.com')
            ->subject('Hello from Symfony!')
            ->text('This is a test email.');

        $this->mailer->send($email);

        return new Response('Email sent!');
    }

}

