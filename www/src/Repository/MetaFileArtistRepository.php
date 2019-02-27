<?php

namespace App\Repository;

use App\Entity\MetaFileArtist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MetaFileArtist|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaFileArtist|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaFileArtist[]    findAll()
 * @method MetaFileArtist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaFileArtistRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MetaFileArtist::class);
    }
}
