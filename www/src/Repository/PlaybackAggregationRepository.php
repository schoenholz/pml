<?php

namespace App\Repository;

use App\Entity\PlaybackAggregation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PlaybackAggregation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaybackAggregation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaybackAggregation[]    findAll()
 * @method PlaybackAggregation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaybackAggregationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlaybackAggregation::class);
    }
}
