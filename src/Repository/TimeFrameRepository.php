<?php

namespace App\Repository;

use App\Entity\TimeFrame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeFrame>
 *
 * @method TimeFrame|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeFrame|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeFrame[]    findAll()
 * @method TimeFrame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeFrameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeFrame::class);
    }

//    /**
//     * @return TimeFrames[] Returns an array of TimeFrames objects
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

//    public function findOneBySomeField($value): ?TimeFrames
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
