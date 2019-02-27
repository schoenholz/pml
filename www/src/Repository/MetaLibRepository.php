<?php

namespace App\Repository;

use App\Entity\MetaLib;
use App\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MetaLib|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaLib|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaLib[]    findAll()
 * @method MetaLib[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaLibRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MetaLib::class);
    }

    public function requireOneBy(
        string $field,
        string $value
    ): MetaLib {
        try {
            return $this
                ->createQueryBuilder('m')
                ->andWhere('m.' . $field . '= :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getSingleResult()
                ;
        } catch (NoResultException $e) {
            throw new EntityNotFoundException($this->getEntityName(), $field, $value, $e);
        }
    }
}
