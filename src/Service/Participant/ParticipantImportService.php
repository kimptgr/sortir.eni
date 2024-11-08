<?php

namespace App\Service\Participant;

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
        $firstName = $data[1];
        $lastName = $data[2];
        $password = $data[3];
        $roles = explode(',', $data[4]);

        $user = new Participant();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles($roles);
        $user->setIsActive(true);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
