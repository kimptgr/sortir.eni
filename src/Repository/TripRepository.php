<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Trip;
use App\Form\TripFilterType;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use mysql_xdevapi\Statement;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function findTripByFilters(mixed $filterChoices, Participant $userInSession): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($filterChoices['relativeCampus'] !== null) {
            $qb->andWhere('t.relativeCampus = :campus')
                ->setParameter('campus', $filterChoices['relativeCampus']);
        }

        if (!empty($filterChoices['tripName'])) {
            $qb->andWhere('t.name LIKE :tripName')
                ->setParameter('tripName', '%' . $filterChoices['tripName'] . '%');
        }

        if (!empty($filterChoices['startDateTime'])) {
            $stringStartDateTime = $filterChoices['startDateTime']->format('Y-m-d H:i:s');
            $qb->andWhere('t.startDateTime >= :startDateTime')
                ->setParameter('startDateTime', $stringStartDateTime);
        }

        if (!empty($filterChoices['registrationDeadline'])) {
            $stringregistrationDeadline = $filterChoices['registrationDeadline']->format('Y-m-d H:i:s');
            $qb->andWhere('t.registrationDeadline <= :registrationDeadline')
                ->setParameter('registrationDeadline', $stringregistrationDeadline);
        }

        if ($filterChoices['iOrganized']) {
            $qb->andWhere('t.organizer = :$organizer')
                ->setParameter('$organizer', $userInSession);
        }
        if ($filterChoices['iParticipate']) {
            $qb->andWhere('t.participants = :$participants')
                ->setParameter('$participants', $userInSession);
        }
        if ($filterChoices['imRegistered']) {
            $qb->andWhere('t.participants = :$participants')
                ->setParameter('$participants', $userInSession);
        }
        if ($filterChoices['oldTrips']) {
            $now = (new dateTime())->format('Y-m-d H:i:s');
           // $dateEnd = $startDateTime + $duration
            $qb->andWhere('t.registrationDeadline < :registrationDeadline')
                ->setParameter('registrationDeadline', $now);
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Trip[] Returns an array of Trip objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Trip
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
