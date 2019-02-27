<?php

namespace App\Repository;

use App\Entity\MetaFileGenre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MetaFileGenre|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaFileGenre|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaFileGenre[]    findAll()
 * @method MetaFileGenre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaFileGenreRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MetaFileGenre::class);
    }
}
