<?php

namespace App\Command;

use App\PlaylistManager;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildObsessionsPlaylistCommand extends Command
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
            ->setName('app:playlist:build:obsessions')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $con = $this->entityManager->getConnection();
        $stmt = $con->prepare("
            SELECT
                f.id AS file_id
            
            FROM (
            
                SELECT
                    s.id AS song_id,
                    s.max_playback_percentage_date AS obsession_date
            
                FROM song s
            
                WHERE
                    s.max_playback_percentage > (
                        SELECT
                        AVG(max_playback_percentage) * 2.1
                        FROM song
                        WHERE
                            rating >= 50
                    )
                    AND EXISTS (
                        SELECT 
                            MIN(from_date)
                        FROM playback_aggregation
                        WHERE
                            song_id = s.id
                            AND percentage = s.max_playback_percentage
                            AND play_count > 0.42 * DATEDIFF(to_date, from_date) -- ignore small play counts
                    )
            
                UNION
            
                SELECT
                    pa.song_id AS song_id,
                    MIN(pa.from_date) AS obsession_date
            
                FROM playback_aggregation pa
            
                WHERE
                    (pa.count / DATEDIFF(pa.to_date, pa.from_date)) > 0.75
            
                GROUP BY
                    pa.song_id
            ) AS obsessions
            
            INNER JOIN song s 
            ON s.id = obsessions.song_id
            AND (
                s.rating > 80 -- include only favourites
                OR s.rating IS NULL
            )
            
            INNER JOIN file f 
            ON f.song_id = s.id
            AND f.song_relation = 'primary'
            
            GROUP BY 
                f.id
            
            ORDER BY 
                MIN(obsessions.obsession_date) ASC
        ");
        $stmt->execute();
        $fileIds = $stmt->fetchAll(FetchMode::COLUMN);

        $this->playlistManager->createPlaylist('Obsessions', $fileIds);
    }
}
