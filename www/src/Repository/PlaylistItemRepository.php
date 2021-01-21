<?php

namespace App\Repository;

use App\Entity\PlaylistItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PlaylistItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaylistItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaylistItem[]    findAll()
 * @method PlaylistItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlaylistItem::class);
    }
}
