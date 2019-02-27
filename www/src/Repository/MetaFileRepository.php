<?php

namespace App\Repository;

use App\Entity\MetaFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MetaFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaFile[]    findAll()
 * @method MetaFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaFileRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MetaFile::class);
    }
}
