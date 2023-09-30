<?php

namespace App\Repository;

use App\Entity\SchoolClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolClass>
 *
 * @method SchoolClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolClass[]    findAll()
 * @method SchoolClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolClass::class);
    }

//    /**
//     * @return SchoolClasses[] Returns an array of SchoolClasses objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SchoolClasses
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findByQuery(mixed $str) :?array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.code LIKE :q')
            ->setParameter('q', $str.'%')
            ->getQuery()
            ->getResult();
    }
}
