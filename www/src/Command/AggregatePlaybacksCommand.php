<?php

namespace App\Command;

use App\Entity\MetaFileTouch;
use App\Entity\PlaybackAggregation;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AggregatePlaybacksCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Connection
     */
    private $con;

    /**
     * @var \DateTime
     */
    private $now;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:lib:aggregate-playbacks')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->con = $this->entityManager->getConnection();
        $this->now = new \DateTime();
        $this->output = $output;

        $firstPlaybackDate = $this->getFirstPlaybackDate();

        $periods = [];
        for ($i = 1; $i <= 6; $i ++) {
            $this->aggregateWeekly($firstPlaybackDate, $i, $periods);
        }

        $this->purgeAggregationPeriods($periods);
    }

    private function aggregateWeekly(
        \DateTime $base,
        int $numberOfWeeks,
        array &$periods
    ) {
        $from = clone $base;
        $interval = new \DateInterval(sprintf('P%dW', $numberOfWeeks));

        while (true) {
            $to = clone $from;
            $to->add($interval);

            if ($to > $this->now) {
                break;
            }

            $this->output->writeln(sprintf('Aggregating playbacks between %s and %s', $from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s')));

            $period = sprintf('W%d-%s', $numberOfWeeks, $from->format('Y-m'));
            $songPlaybacks = $this->getSongPlaybacks($from, $to);
            $songIds = array_column($songPlaybacks, 'song_id');
            $totalCount = array_sum(array_column($songPlaybacks, 'playbacks'));

            $this->writeAggregations($period, $from, $to, $totalCount, $songPlaybacks);
            $this->purgeAggregations($period, $songIds);

            $periods[] = $period;

            // Advance to next period
            $from->add($interval);
        }
    }

    private function writeAggregations(
        string $period,
        \DateTime $from,
        \DateTime $to,
        int $totalCount,
        array $songPlaybacks
    ) {
        if (!empty($songPlaybacks)) {
            $i = 0;
            $inserts = [];
            $insertValues = [];
            foreach ($songPlaybacks as $playback) {
                $insertValues[':period_' . $i] = $period;
                $insertValues[':from_date_' . $i] = $from->format('Y-m-d H:i:s');
                $insertValues[':to_date_' . $i] = $to->format('Y-m-d H:i:s');
                $insertValues[':song_id_' . $i] = $playback['song_id'];
                $insertValues[':count_' . $i] = $playback['playbacks'];
                $insertValues[':total_count_' . $i] = $totalCount;
                $insertValues[':percentage_' . $i] = $playback['playbacks'] / $totalCount * 100;

                $inserts[] = sprintf('(:period_%d, :from_date_%d, :to_date_%d, :song_id_%d, :count_%d, :total_count_%d, :percentage_%d)', $i, $i, $i, $i, $i, $i, $i);

                $i ++;
            }

            $sql = '
                INSERT INTO playback_aggregation (period, from_date, to_date, song_id, count, total_count, percentage) 
                VALUES ' . implode(', ', $inserts) .'
                ON DUPLICATE KEY UPDATE count = VALUES(count), total_count = VALUES(total_count), percentage = VALUES(percentage)
            ';

            $stmt = $this->con->prepare($sql);
            $stmt->execute($insertValues);
        }
    }

    private function purgeAggregations(
        string $period,
        array $songIds
    ) {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(PlaybackAggregation::class, 'pa');
        $qb->where('pa.period = :period');
        $qb->setParameter('period', $period);

        if (!empty($songIds)) {
            $qb->andWhere('pa.song NOT IN (:song_ids)');
            $qb->setParameter('song_ids', $songIds);
        }

        $qb->getQuery()->execute();
    }

    private function purgeAggregationPeriods(array $periods)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(PlaybackAggregation::class, 'pa');

        if (!empty($periods)) {
            $qb->andWhere('pa.period NOT IN (:periods)');
            $qb->setParameter('periods', $periods);
        }

        $qb->getQuery()->execute();
    }

    private function getSongPlaybacks(
        \DateTime $from,
        \DateTime $to
    ): array {
        $stmt = $this->con->prepare('
            SELECT
                s.id AS song_id,
                SUM(count) AS playbacks
            
            FROM song s
            
            INNER JOIN file f
            ON f.song_id = s.id
            
            INNER JOIN meta_file mf
            ON mf.file_id = f.id
            
            INNER JOIN meta_file_touch mft
            ON mft.meta_file_id = mf.id
            AND mft.type = :type
            AND mft.date >= :from
            AND mft.date < :to
            
            GROUP BY
                s.id
        ');
        $stmt->execute([
            'type' => MetaFileTouch::TYPE_PLAY,
            'from' => $from->format('Y-m-d H:i:s'),
            'to' => $to->format('Y-m-d H:i:s'),
        ]);

        return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
    }

    private function getFirstPlaybackDate(): \DateTime
    {
        $stmt = $this->con->prepare('
            SELECT MIN(date)
            FROM meta_file_touch
            WHERE
                type = :type
        ');
        $stmt->execute([
            'type' => MetaFileTouch::TYPE_PLAY,
        ]);

        $dateStr = $stmt->fetch(FetchMode::COLUMN);

        $date = new \DateTime($dateStr);
        $date->setTime(0, 0,0 );

        while ($date->format('N') != 1) {
            $date->sub(new \DateInterval('P1D'));
        }

        return $date;
    }
}
