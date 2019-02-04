<?php

namespace App\Repository;

use App\Entity\AppEntitySong;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AppEntitySong|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppEntitySong|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppEntitySong[]    findAll()
 * @method AppEntitySong[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppEntitySongRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AppEntitySong::class);
    }

//    /**
//     * @return AppEntitySong[] Returns an array of AppEntitySong objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AppEntitySong
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
