<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Trip;
use App\Form\TripFilterType;
use App\Models\TripFilterModel;
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

    public function findDateTime(){
        $dateTime = new DateTime("now");
        $querrybuilder = $this->createQueryBuilder('trip')
            ->addSelect('trip.startDateTime >= :dateTime')
            ->setParameter('dateTime', $dateTime);
    }

    public function findTripByFilters(TripFilterModel $filterChoices, Participant $userInSession): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($filterChoices->getRelativeCampus() !== null) {
            $qb->andWhere('t.relativeCampus = :campus')
                ->setParameter('campus', $filterChoices->getRelativeCampus());
        }

        if (!empty($filterChoices->getTripName())) {
            $qb->andWhere('t.name LIKE :tripName')
                ->setParameter('tripName', '%' . $filterChoices->getTripName() . '%');
        }

        if (!empty($filterChoices->getStartDateTime())) {
            $stringStartDateTime = $filterChoices->getStartDateTime()->format('Y-m-d H:i:s');
            $qb->andWhere('t.startDateTime >= :startDateTime')
                ->setParameter('startDateTime', $stringStartDateTime);
        }

        if (!empty($filterChoices->getRegistrationDeadline())) {
            $stringregistrationDeadline = $filterChoices->getRegistrationDeadline()->format('Y-m-d H:i:s');
            $qb->andWhere('t.registrationDeadline <= :registrationDeadline')
                ->setParameter('registrationDeadline', $stringregistrationDeadline);
        }

        if ($filterChoices->getIOrganized()) {
            $qb->andWhere('t.organizer = :organizer')
                ->setParameter('organizer', $userInSession);
        }
        if ($filterChoices->getIParticipate()) {
            $qb->join('t.participants', 'p')
                ->andWhere(':participants MEMBER OF t.participants')
                ->setParameter('participants', $userInSession);
        }
        if ($filterChoices->getImRegistered()) {
            $qb->join('t.participants', 'pa')
                ->andWhere(':participants NOT MEMBER OF t.participants')
                ->setParameter('participants', $userInSession);
        }
        if ($filterChoices->getOldTrips()) {
            $now = (new dateTime())->format('Y-m-d H:i:s');
           // $dateEnd = $startDateTime + $duration
            $qb->andWhere('t.registrationDeadline < :registrationDeadline')
                ->setParameter('registrationDeadline', $now);
        }

        return $qb->getQuery()->getResult();
    }

}
