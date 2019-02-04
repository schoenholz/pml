<?php

namespace App\Repository;

use App\Entity\WorkHasSong;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WorkHasSong|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkHasSong|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkHasSong[]    findAll()
 * @method WorkHasSong[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkHasSongRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WorkHasSong::class);
    }

//    /**
//     * @return WorkHasSong[] Returns an array of WorkHasSong objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WorkHasSong
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
