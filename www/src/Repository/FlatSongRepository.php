<?php

namespace App\Repository;

use App\Entity\FlatSong;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FlatSong|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlatSong|null findOneBy(array $criteria, array $orderBy = null)
 * @method FlatSong[]    findAll()
 * @method FlatSong[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlatSongRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FlatSong::class);
    }

    // /**
    //  * @return FlatSong[] Returns an array of FlatSong objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FlatSong
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
