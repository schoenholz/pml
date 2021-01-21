<?php

namespace App\Command;

use App\PlaylistManager;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildFlashingTunesPlaylistCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PlaylistManager
     */
    private $playlistManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        PlaylistManager $playlistManager
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->playlistManager = $playlistManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:playlist:build:flashing-tunes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $con = $this->entityManager->getConnection();
        $stmt = $con->prepare("
            SELECT
                f_primary.id AS file_id,
                f_primary.*
                
                FROM (
                    SELECT
                        s.id AS song_id,
                    --	MAX(mf.title),
                    --	SUM(mft.count) AS play_count,
                        MIN(mft.date) AS first_play_date
                    --	MAX(mft.date),
                    --	DATEDIFF(MAX(mft.date), MIN(mft.date)) + 1,
                    --	SUM(mft.count) / (DATEDIFF(MAX(mft.date), MIN(mft.date)) + 1)
                    
                    FROM song s
                    
                    INNER JOIN file f
                    ON f.song_id = s.id
                    
                    INNER JOIN meta_file mf
                    ON mf.file_id = f.id
                    
                    INNER JOIN meta_file_touch mft
                    ON mft.meta_file_id = mf.id
                    AND mft.type = 'play'
                    AND mft.date IS NOT NULL
                    AND DATEDIFF(mft.date, s.first_play_date) <= 21
                    
                    WHERE
                        s.first_play_date IS NOT NULL
                        
                    GROUP BY
                        s.id
                    
                    HAVING
                        SUM(mft.count) >= 7
                        AND SUM(mft.count) / (DATEDIFF(MAX(mft.date), MIN(mft.date)) + 1) >= 0.5
                    
                    ORDER BY
                        first_play_date ASC,
                        s.id ASC
                ) AS tmp
                
                INNER JOIN file f_primary
                ON f_primary.song_id = tmp.song_id
                AND f_primary.song_relation = 'primary'

                ORDER BY
                    tmp.first_play_date ASC,
                    tmp.song_id ASC
        ");
        $stmt->execute();
        $fileIds = $stmt->fetchAll(FetchMode::COLUMN);

        $this->playlistManager->createPlaylist('Flashing-Tunes', $fileIds);
    }
}
