<?php

namespace App\Command;

use App\ArtistSplitter;
use App\Entity\File;
use App\Entity\MetaFile;
use App\Entity\MetaFileArtist;
use App\Entity\MetaFileGenre;
use App\Entity\MetaFileTouch;
use App\Entity\Song;
use App\Exception\EntityNotFoundException;
use App\MetaFileWriter;
use App\Repository\MetaLibRepository;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportLastFmSongsCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MetaFileWriter
     */
    private $metaFileWriter;

    /**
     * @var MetaLibRepository
     */
    private $metaLibRepository;

    /**
     * @var ArtistSplitter
     */
    private $artistSplitter;

    public function __construct(
        EntityManagerInterface $entityManager,
        MetaLibRepository $metaLibRepository,
        MetaFileWriter $metaFileWriter,
        ArtistSplitter $artistSplitter
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->metaFileWriter = $metaFileWriter;
        $this->metaLibRepository = $metaLibRepository;
        $this->artistSplitter = $artistSplitter;
    }

    protected function configure()
    {
        $this
            ->setName('app:songs:import:last_fm')
            ->setDescription('Build songs from last.fm playbacks.')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $usersStmt = $this->entityManager->getConnection()->prepare("
            SELECT DISTINCT user
            FROM last_fm_playback
        ");
        $usersStmt->execute();
        $users = $usersStmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($users as $user) {
            try {
                $metaLib = $this->metaLibRepository->requireOneBy('name', 'last.fm User ' . $user);
            } catch (EntityNotFoundException $e) {
                $output->writeln(sprintf('<error>Skipping last.fm user "%s": no meta lib found.</error>', $user));

                continue;
            }

            $lastFmSongsStmt = $this->entityManager->getConnection()->prepare("
                SELECT
                    artist_title,
                    artist_mbid,
                    track_title,
                    track_mbid,
                    MIN(date) AS first_playback_date
                    
                FROM last_fm_playback
                    
                WHERE
                    user = :user
                    
                GROUP BY 
                    artist_title,
                    artist_mbid,
                    track_title,
                    track_mbid
                    
                ORDER BY NULL
            ");
            $lastFmSongsStmt->execute([
                'user' => $user,
            ]);
            $lastFmSongs = $lastFmSongsStmt->fetchAll(FetchMode::ASSOCIATIVE);
            $totalCount = count($lastFmSongs);

            $output->writeln(sprintf(
                '<info>Importing </info><comment>%d</comment><info> songs from last.fm playback data for user "<comment>%s</comment>" into meta lib "</info><comment>%s</comment><info>".</info>',
                $totalCount,
                $user,
                $metaLib->getName()
            ));

            $progressBar = new ProgressBar($output, $totalCount);
            $progressBar->start();

            foreach ($lastFmSongs as $lastFmSong) {
                $progressBar->advance();

                $id = implode('-+-' ,[
                    'last.fm:',
                    $lastFmSong['artist_title'],
                    $lastFmSong['artist_mbid'],
                    $lastFmSong['track_title'],
                    $lastFmSong['track_mbid'],
                ]);

                $artistTitles = $this->artistSplitter->split($lastFmSong['artist_title'], $lastFmSong['track_title']);

                // Always keep the original artist to prevent from splitting failures
                if (!in_array($lastFmSong['artist_title'], $artistTitles)) {
                    $artistTitles[] = $lastFmSong['artist_title'];
                }

                $artists = array_map(function (string $artistTitle) use ($lastFmSong) {
                    return [
                        'title' => $artistTitle,
                        'music_brainz_id' => $artistTitle == $lastFmSong['artist_title'] ? (
                            !empty($lastFmSong['artist_mbid'])
                                ? $lastFmSong['artist_mbid']
                                : null
                        ) : null,
                    ];
                }, $artistTitles);

                $data = [
                    'file_path_name' => $id,
                    'external_id' => $id,
                    'is_synthetic' => true,

                    'added_date' => new \DateTime($lastFmSong['first_playback_date']),
                    'album' => null,
                    'bitrate' => null,
                    'bpm' => null,
                    'date' => null,
                    'disc_number' => null,
                    'initial_key' => null,
                    'music_brainz_id' => !empty($lastFmSong['track_mbid']) ? $lastFmSong['track_mbid'] : null,
                    'publisher' => null,
                    'rating' => null,
                    'sampling_frequency' => null,
                    'title' => $lastFmSong['track_title'],
                    'track_number' => null,
                    'year' => null,

                    'artists' => $artists,
                    'genres' => [],
                    'play_dates' => $this->findLastFmPlayDates(
                        $user,
                        $lastFmSong['artist_title'],
                        $lastFmSong['artist_mbid'],
                        $lastFmSong['track_title'],
                        $lastFmSong['track_mbid']
                    ),
                ];

                $this->metaFileWriter->writeMetaFile($metaLib, $data);

                $this->entityManager->clear(Song::class);
                $this->entityManager->clear(File::class);
                $this->entityManager->clear(MetaFile::class);
                $this->entityManager->clear(MetaFileArtist::class);
                $this->entityManager->clear(MetaFileGenre::class);
                $this->entityManager->clear(MetaFileTouch::class);
            }

            $progressBar->finish();
            $output->writeln('');

            $output->writeln(sprintf('Created %d new songs.', $this->metaFileWriter->getMetaFileCreatedCount()));
            $output->writeln('Done. 🎉');
        }
    }

    private function findLastFmPlayDates(
        string $user,
        string $artistTitle,
        string $artistMbid,
        string $trackTitle,
        string $trackMbid
    ): array {
        $stmt = $this->entityManager->getConnection()->prepare("
            SELECT
                date,
                prec,
                count
            
            FROM last_fm_playback
            
            WHERE
                user = :user 
                AND artist_title = :artist_title
                AND artist_mbid = :artist_mbid
                AND track_title = :track_title
                AND track_mbid = :track_mbid
        ");
        $stmt->execute([
            'user' => $user,
            'artist_title' => $artistTitle,
            'artist_mbid' => $artistMbid,
            'track_title' => $trackTitle,
            'track_mbid' => $trackMbid,
        ]);

        return array_map(function (array $data) {
            $data['date'] = new \DateTime($data['date']);

            return $data;
        }, $stmt->fetchAll(FetchMode::ASSOCIATIVE));
    }
}
