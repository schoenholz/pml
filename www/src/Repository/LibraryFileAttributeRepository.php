<?php

namespace App\Repository;

use App\Entity\LibraryFileAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LibraryFileAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method LibraryFileAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method LibraryFileAttribute[]    findAll()
 * @method LibraryFileAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LibraryFileAttributeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LibraryFileAttribute::class);
    }
}
