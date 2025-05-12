<?php

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 *
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * Find properties by filters
     */
    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('p');
        
        // Apply city filter
        if (!empty($filters['city'])) {
            $qb->andWhere('p.city LIKE :city')
               ->setParameter('city', '%' . $filters['city'] . '%');
        }
        
        // Apply price range filter
        if (!empty($filters['minPrice'])) {
            $qb->andWhere('p.pricePerNight >= :minPrice')
               ->setParameter('minPrice', $filters['minPrice']);
        }
        
        if (!empty($filters['maxPrice'])) {
            $qb->andWhere('p.pricePerNight <= :maxPrice')
               ->setParameter('maxPrice', $filters['maxPrice']);
        }
        
        // Apply property type filter
        if (!empty($filters['propertyType'])) {
            $qb->andWhere('p.propertyType = :propertyType')
               ->setParameter('propertyType', $filters['propertyType']);
        }
        
        // Apply equipment filters
        if (!empty($filters['equipment'])) {
            $qb->leftJoin('p.equipment', 'e')
               ->andWhere('e.id IN (:equipment)')
               ->setParameter('equipment', $filters['equipment']);
        }
        
        // Apply service filters
        if (!empty($filters['services'])) {
            $qb->leftJoin('p.services', 's')
               ->andWhere('s.id IN (:services)')
               ->setParameter('services', $filters['services']);
        }
        
        // Search by name
        if (!empty($filters['search'])) {
            $qb->andWhere('p.name LIKE :search OR p.description LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }
        
        return $qb->getQuery()->getResult();
    }
}