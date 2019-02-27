<?php

namespace App\Repository;

use App\Entity\MetaFileRating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MetaFileRating|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaFileRating|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaFileRating[]    findAll()
 * @method MetaFileRating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaFileRatingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MetaFileRating::class);
    }
}
