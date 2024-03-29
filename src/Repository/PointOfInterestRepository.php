<?php

namespace App\Repository;

use App\Entity\PointOfInterest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PointOfInterest>
 *
 * @method PointOfInterest|null find($id, $lockMode = null, $lockVersion = null)
 * @method PointOfInterest|null findOneBy(array $criteria, array $orderBy = null)
 * @method PointOfInterest[]    findAll()
 * @method PointOfInterest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PointOfInterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PointOfInterest::class);
    }

    public function save(PointOfInterest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PointOfInterest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
