<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Find completed stays that haven't been reviewed yet
     */
    public function findCompletedStaysWithoutReview(User $user): array
    {
        $today = new \DateTime();
        
        return $this->createQueryBuilder('b')
            ->andWhere('b.user = :user')
            ->andWhere('b.endDate < :today')
            ->andWhere('b.hasReviewed = false OR b.hasReviewed IS NULL')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->orderBy('b.endDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all bookings for a user
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}