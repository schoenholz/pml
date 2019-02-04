<?php

namespace App\Repository;

use App\Entity\SongTouch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SongTouch|null find($id, $lockMode = null, $lockVersion = null)
 * @method SongTouch|null findOneBy(array $criteria, array $orderBy = null)
 * @method SongTouch[]    findAll()
 * @method SongTouch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongTouchRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SongTouch::class);
    }

//    /**
//     * @return SongTouch[] Returns an array of SongTouch objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SongTouch
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
