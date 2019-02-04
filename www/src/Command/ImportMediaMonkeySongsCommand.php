<?php

namespace App\Command;

use App\Entity\MediaMonkeySong;
use App\Entity\Song;
use App\Manager\MediaMonkeyDatabaseManager;
use App\MediaMonkeyDatabase;
use App\Repository\MediaMonkeySongRepository;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportMediaMonkeySongsCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MediaMonkeyDatabaseManager
     */
    private $mediaMonkeyDatabaseManager;

    /**
     * @var MediaMonkeyDatabase
     */
    private $mediaMonkeyDatabase;

    public function __construct(
        EntityManagerInterface $entityManager,
        MediaMonkeyDatabaseManager $mediaMonkeyDatabaseManager,
        MediaMonkeyDatabase $mediaMonkeyDatabase
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->mediaMonkeyDatabaseManager = $mediaMonkeyDatabaseManager;
        $this->mediaMonkeyDatabase = $mediaMonkeyDatabase;
    }

    protected function configure()
    {
        $this
            ->setName('app:songs:import:media_monkey')
            ->setDescription('Imports songs from MediaMonkey DB.')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $output->writeln('<info>Importing songs from MediaMonkey database.</info>');

        $this->assertDatabaseIsValid();

        $remoteIds = $this
            ->mediaMonkeyDatabaseManager
            ->fetchSongIds()
        ;
        $totalCount = count($remoteIds);
        $createdCount = 0;

        /** @var MediaMonkeySongRepository $mediaMonkeySongRepo */
        $mediaMonkeySongRepo = $this->entityManager->getRepository(MediaMonkeySong::class);

        $progressBar = new ProgressBar($output, $totalCount);
        $progressBar->start();

        foreach ($remoteIds as $mediaMonkeyId) {
            $progressBar->advance();
            $mediaMonkeyData = $this
                ->mediaMonkeyDatabaseManager
                ->fetchSongData($mediaMonkeyId)
            ;

            $mediaMonkeySong = $mediaMonkeySongRepo->findOneBy([
                'mediaMonkeyId' => $mediaMonkeyId,
            ]);

            if (!$mediaMonkeySong) {
                $mediaMonkeySong = new MediaMonkeySong();
                $createdCount ++;
            }

            natcasesort($mediaMonkeyData['artists']);
            natcasesort($mediaMonkeyData['genres']);

            $mediaMonkeySong
                ->setAddedDate($mediaMonkeyData['added_date'])
                ->setAlbum($mediaMonkeyData['album'])
                ->setArtist($mediaMonkeyData['artists'])
                ->setBitrate($mediaMonkeyData['bitrate'])
                ->setBpm($mediaMonkeyData['bpm'])
                ->setDate($mediaMonkeyData['date'])
                ->setDeletionDate(null)
                ->setDiscNumber($mediaMonkeyData['disc_number'])
                ->setFilePathName($mediaMonkeyData['file_path_name'])
                ->setFirstPlayedDate($mediaMonkeyData['first_played_date'])
                ->setGenre($mediaMonkeyData['genres'])
                ->setInitialKey($mediaMonkeyData['initial_key'])
                ->setIsDeleted(false)
                ->setLastPlayedDate($mediaMonkeyData['last_played_date'])
                ->setMediaMonkeyId($mediaMonkeyId)
                ->setPlayCount($mediaMonkeyData['play_count'] ?? 0)
                ->setPublisher($mediaMonkeyData['publisher'])
                ->setRating($mediaMonkeyData['rating'])
                ->setSamplingFrequency($mediaMonkeyData['sampling_frequency'])
                ->setSkipCount($mediaMonkeyData['skip_count'] ?? 0)
                ->setTitle($mediaMonkeyData['title'])
                ->setTrackNumber($mediaMonkeyData['track_number'])
                ->setYear($mediaMonkeyData['year'])
            ;

            $this->entityManager->persist($mediaMonkeySong);
            $this->entityManager->flush();
            $this->entityManager->clear(MediaMonkeySong::class);
        }

        $progressBar->finish();
        $output->writeln('');

        // Mark unknown songs an deleted
        $qb = $this
            ->entityManager
            ->createQueryBuilder()
        ;
        $deletedCount = $qb
            ->update(MediaMonkeySong::class, 'mms')
            ->set('mms.isDeleted', ':isDeleted')
            ->set('mms.deletionDate', ':deletionDate')
            ->where('mms.isDeleted = 0')
            ->andWhere('mms.mediaMonkeyId NOT IN (:ids)')
            ->setParameters([
                'ids' => $remoteIds,
                'isDeleted' => true,
                'deletionDate' => new \DateTime(),
            ])
            ->getQuery()
            ->execute()
        ;

        $output->writeln(sprintf('Created %d new songs.', $createdCount));
        $output->writeln(sprintf('Marked %d songs as deleted.', $deletedCount));
    }

    private function assertDatabaseIsValid()
    {
        // Check if any song exists in local database
        $stmtCount = $this
            ->entityManager
            ->getConnection()
            ->prepare('SELECT COUNT(1) FROM media_monkey_song')
        ;
        $stmtCount->execute();

        if ($stmtCount->fetch(FetchMode::COLUMN) == 0) {
            return;
        }

        $stmtMediaMonkey = $this
            ->mediaMonkeyDatabase
            ->getConnection()
            ->prepare('
                SELECT
                    MAX(datetime(julianday(Songs.DateAdded) + julianday("1899-12-30"), "localtime")),
                    MAX(
                        CASE
                            WHEN LastTimePlayed > 0 
                            THEN datetime(julianday(Songs.LastTimePlayed) + julianday("1899-12-30"), "localtime") 
                            ELSE NULL 
                        END
                    )
                    
                FROM Songs
            ')
        ;
        $stmtMediaMonkey->execute();
        list($maxAddedDateMediaMonkey, $maxLastPlayedDateMediaMonkey) = $stmtMediaMonkey->fetch(FetchMode::NUMERIC);
        $maxAddedDateMediaMonkey = new \DateTime($maxAddedDateMediaMonkey);
        $maxLastPlayedDateMediaMonkey = new \DateTime($maxLastPlayedDateMediaMonkey);

        $stmtLocal = $this
            ->entityManager
            ->getConnection()
            ->prepare('
                SELECT 
                    MAX(added_date),
                    MAX(last_played_date)
                    
                FROM media_monkey_song
            ')
        ;
        $stmtLocal->execute();
        list($maxAddedDateLocal, $maxLastPlayedDateLocal) = $stmtLocal->fetch(FetchMode::NUMERIC);
        $maxAddedDateLocal = new \DateTime($maxAddedDateLocal);
        $maxLastPlayedDateLocal = new \DateTime($maxLastPlayedDateLocal);

        if ($maxAddedDateLocal > $maxAddedDateMediaMonkey) {
            throw new \RuntimeException(sprintf(
                'Max added_date is greater in local DB (%s) than in MediaMonkey DB (%s)',
                $maxAddedDateLocal->format('Y-m-d H:i:s'),
                $maxAddedDateMediaMonkey->format('Y-m-d H:i:s')
            ));
        }

        if ($maxLastPlayedDateLocal > $maxLastPlayedDateMediaMonkey) {
            throw new \RuntimeException(sprintf(
                'Max last_played_date is greater in local DB (%s) than in MediaMonkey DB (%s)',
                $maxLastPlayedDateLocal->format('Y-m-d H:i:s'),
                $maxLastPlayedDateMediaMonkey->format('Y-m-d H:i:s')
            ));
        }
    }
}
