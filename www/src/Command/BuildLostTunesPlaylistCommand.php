<?php

namespace App\Command;

use App\PlaylistManager;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildLostTunesPlaylistCommand extends Command
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
            ->setName('app:playlist:build:lost-tunes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $con = $this->entityManager->getConnection();
        $stmt = $con->prepare("
            SELECT
                tmp.id
            
            FROM (
                SELECT
                    f.id,
                    f.file_path_name,
                    (
                        s.rating_score * 2
                        + s.play_count_score 
                        + s.max_playback_percentage_score * 2
                        + s.playback_aggregation_period_occurrence_quota_score
                        + s.played_per_touch_quota_score * 3
                    ) / 9 AS bar,
                    s.last_touch_date_score
            
                FROM song s
            
                INNER JOIN file f
                ON f.song_id = s.id
                AND f.song_relation = 'primary'
            
                WHERE
                    s.last_play_date IS NOT NULL
                    
                HAVING
                    bar >= 60
            ) AS tmp
            
            ORDER BY
                tmp.last_touch_date_score ASC
                
            LIMIT 200
        ");
        $stmt->execute();
        $fileIds = $stmt->fetchAll(FetchMode::COLUMN);

        $this->playlistManager->createPlaylist('Lost-Tunes', $fileIds);
    }
}
