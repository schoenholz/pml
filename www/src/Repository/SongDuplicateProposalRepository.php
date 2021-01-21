<?php

namespace App\Repository;

use App\Entity\SongDuplicateProposal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SongDuplicateProposal|null find($id, $lockMode = null, $lockVersion = null)
 * @method SongDuplicateProposal|null findOneBy(array $criteria, array $orderBy = null)
 * @method SongDuplicateProposal[]    findAll()
 * @method SongDuplicateProposal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongDuplicateProposalRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SongDuplicateProposal::class);
    }
}
