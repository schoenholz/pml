<?php

namespace App\Repository;

use App\Entity\SongArtist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SongArtist|null find($id, $lockMode = null, $lockVersion = null)
 * @method SongArtist|null findOneBy(array $criteria, array $orderBy = null)
 * @method SongArtist[]    findAll()
 * @method SongArtist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongArtistRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SongArtist::class);
    }

//    /**
//     * @return SongArtist[] Returns an array of SongArtist objects
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
    public function findOneBySomeField($value): ?SongArtist
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
