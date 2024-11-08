<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Models\ParticipantFilterModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Participant>
 */
class ParticipantRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Participant) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }


    public function findParticipantsByFilters(ParticipantFilterModel $filterModel): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($filterModel->getUsername()) {
            $qb->andWhere('p.pseudo LIKE :username')
                ->setParameter('username', '%' . $filterModel->getUsername() . '%');
        }

        if ($filterModel->getEmail()) {
            $qb->andWhere('p.email LIKE :email')
                ->setParameter('email', '%' . $filterModel->getEmail() . '%');
        }

        if ($filterModel->getRole()) {
            $qb->andWhere('p.roles LIKE :role')
                ->setParameter('role', '%' . $filterModel->getRole() . '%');
        }

        if ($filterModel->getIsActive() !== null) {
            $qb->andWhere('p.isActive = :isActive')
                ->setParameter('isActive', $filterModel->getIsActive());
        }

        return $qb->getQuery()->getResult();
    }



    //    /**
    //     * @return Participant[] Returns an array of Participant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Participant
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
