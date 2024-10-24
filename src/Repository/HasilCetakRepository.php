<?php

namespace App\Repository;

use App\Entity\HasilCetak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HasilCetak>
 *
 * @method HasilCetak|null find($id, $lockMode = null, $lockVersion = null)
 * @method HasilCetak|null findOneBy(array $criteria, array $orderBy = null)
 * @method HasilCetak[]    findAll()
 * @method HasilCetak[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HasilCetakRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HasilCetak::class);
    }

//    /**
//     * @return HasilCetak[] Returns an array of HasilCetak objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HasilCetak
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
