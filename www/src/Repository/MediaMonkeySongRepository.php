<?php

namespace App\Repository;

use App\Entity\MediaMonkeySong;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MediaMonkeySong|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaMonkeySong|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaMonkeySong[]    findAll()
 * @method MediaMonkeySong[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaMonkeySongRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MediaMonkeySong::class);
    }

//    /**
//     * @return MediaMonkeySong[] Returns an array of MediaMonkeySong objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MediaMonkeySong
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
