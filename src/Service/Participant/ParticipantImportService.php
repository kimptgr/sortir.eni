<?php

namespace App\Service\Participant;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantImportService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function importUsersFromCsv(string $filePath): void
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \Exception('Invalid file path.');
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $this->createUserFromData($data);
            }
            fclose($handle);
        }
    }

    private function createUserFromData(array $data): void
    {
        $email = $data[0];
        $roles = explode(',', $data[1]);
        $password = $data[2];
        $lastName = $data[3];
        $firstName = $data[4];
        $pseudo = $data[9];
        $phoneNumber = $data[5];
        $campusId = $data[7]; // ID du campus

        // Récupérer le campus via son repository
        $campus = $this->entityManager->getRepository(Campus::class)->find(53);

        // Vérifier si le campus existe, sinon gérer l'erreur
        if (!$campus) {
            throw new \Exception('Campus non trouvé pour l\'ID ' . $campusId);
        }

        // Créer un nouvel utilisateur
        $user = new Participant();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles($roles);
        $user->setActive(true);
        $user->setPseudo($pseudo);
        $user->setPhoneNumber($phoneNumber);
        $user->setCampus($campus);
        $user->setBrochureFilename(null); // Laisser null pour un champ de fichier

        // Hacher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Persister l'utilisateur en base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
