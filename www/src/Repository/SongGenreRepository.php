<?php

namespace App\Repository;

use App\Entity\SongGenre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SongGenre|null find($id, $lockMode = null, $lockVersion = null)
 * @method SongGenre|null findOneBy(array $criteria, array $orderBy = null)
 * @method SongGenre[]    findAll()
 * @method SongGenre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongGenreRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SongGenre::class);
    }

//    /**
//     * @return SongGenre[] Returns an array of SongGenre objects
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
    public function findOneBySomeField($value): ?SongGenre
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
