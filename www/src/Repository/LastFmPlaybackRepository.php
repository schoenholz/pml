<?php

namespace App\Repository;

use App\Entity\LastFmPlayback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\FetchMode;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LastFmPlayback|null find($id, $lockMode = null, $lockVersion = null)
 * @method LastFmPlayback|null findOneBy(array $criteria, array $orderBy = null)
 * @method LastFmPlayback[]    findAll()
 * @method LastFmPlayback[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LastFmPlaybackRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LastFmPlayback::class);
    }

    /**
     * @param string $user
     *
     * @return \DateTime|null
     */
    public function getMaxPlayDate(string $user)
    {
        $stmt = $this->getEntityManager()->getConnection()->prepare("
            SELECT MAX(date)
            FROM last_fm_playback
            WHERE
                user = :user
        ");
        $stmt->execute([
            'user' => $user,
        ]);

        $date = $stmt->fetch(FetchMode::COLUMN);

        if (!$date) {
            return null;
        }

        return new \DateTime($date);
    }

    public function getPlayCount(string $user, \DateTime $from, \DateTime $to): int
    {
        $stmt = $this->getEntityManager()->getConnection()->prepare("
            SELECT SUM(count)
            FROM last_fm_playback
            WHERE
                date >= :from
                AND date <= :to
                AND user = :user
        ");
        $stmt->execute([
            'from' => $from->format('Y-m-d H:i:s'),
            'to' => $to->format('Y-m-d H:i:s'),
            'user' => $user,
        ]);

        return $stmt->fetch(FetchMode::COLUMN);
    }

    /**
     * @param string $user
     *
     * @return \DateTime|null
     */
    public function getMinPlayDate(string $user)
    {
        $stmt = $this->getEntityManager()->getConnection()->prepare("
            SELECT MIN(date)
            FROM last_fm_playback
            WHERE
                user = :user
        ");
        $stmt->execute([
            'user' => $user,
        ]);

        $date = $stmt->fetch(FetchMode::COLUMN);

        if (!$date) {
            return null;
        }

        return new \DateTime($date);
    }
}
