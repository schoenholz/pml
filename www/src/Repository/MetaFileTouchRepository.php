<?php

namespace App\Repository;

use App\Entity\MetaFileTouch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MetaFileTouch|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaFileTouch|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaFileTouch[]    findAll()
 * @method MetaFileTouch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaFileTouchRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MetaFileTouch::class);
    }
}
