<?php

namespace App\Repository;

use App\Entity\PlaylistEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PlaylistEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaylistEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaylistEntry[]    findAll()
 * @method PlaylistEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistEntryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlaylistEntry::class);
    }
}
