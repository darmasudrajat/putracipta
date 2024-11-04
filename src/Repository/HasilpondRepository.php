<?php

namespace App\Repository;

use App\Entity\Hasilpond;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hasilpond>
 *
 * @method Hasilpond|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hasilpond|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hasilpond[]    findAll()
 * @method Hasilpond[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HasilpondRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hasilpond::class);
    }

//    /**
//     * @return Hasilpond[] Returns an array of Hasilpond objects
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

//    public function findOneBySomeField($value): ?Hasilpond
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
