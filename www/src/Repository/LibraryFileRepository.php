<?php

namespace App\Repository;

use App\Entity\LibraryFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LibraryFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method LibraryFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method LibraryFile[]    findAll()
 * @method LibraryFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LibraryFileRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LibraryFile::class);
    }
}
