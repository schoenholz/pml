<?php

namespace App\Command;

use App\Entity\File;
use App\Entity\MetaFile;
use App\Entity\MetaFileArtist;
use App\Entity\MetaFileGenre;
use App\Entity\MetaFileTouch;
use App\Entity\MetaLib;
use App\Entity\Song;
use App\Meta\Lib\Manager\MediaMonkeyDatabaseManager;
use App\MetaFileWriter;
use App\Repository\LastFmPlaybackRepository;
use App\Repository\MetaLibRepository;
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
     * @var MetaLibRepository
     */
    private $metaLibRepository;

    /**
     * @var MediaMonkeyDatabaseManager
     */
    private $mediaMonkeyDatabaseManager;

    /**
     * @var MetaLib
     */
    private $metaLib;

    /**
     * @var \DateTime
     */
    private $lastFmStartDate;
    /**
     * @var LastFmPlaybackRepository
     */
    private $lastFmPlaybackRepository;

    /**
     * @var MetaFileWriter
     */
    private $metaFileWriter;

    public function __construct(
        EntityManagerInterface $entityManager,
        MetaLibRepository $metaLibRepository,
        LastFmPlaybackRepository $lastFmPlaybackRepository,
        MediaMonkeyDatabaseManager $mediaMonkeyDatabaseManager,
        MetaFileWriter $metaFileWriter
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->metaLibRepository = $metaLibRepository;
        $this->lastFmPlaybackRepository = $lastFmPlaybackRepository;
        $this->mediaMonkeyDatabaseManager = $mediaMonkeyDatabaseManager;
        $this->metaFileWriter = $metaFileWriter;
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
        // Setup
        $this->metaLib = $this->metaLibRepository->requireOneBy('name', 'MediaMonkey 4 Dell Laptop');
        $this->assertDatabaseIsValid();
        $this->lastFmStartDate = $this->lastFmPlaybackRepository->getMinPlayDate('t1n3f');

        if (!$this->lastFmStartDate) {
            throw new \RuntimeException('No last.fm play time found. Import last.fm data first.');
        }

        // Import
        $remoteIds = $this
            ->mediaMonkeyDatabaseManager
            ->fetchSongIds()
        ;
        $totalCount = count($remoteIds);

        $output->writeln(sprintf(
            '<info>Importing </info><comment>%d</comment><info> songs from MediaMonkey database into meta lib "</info><comment>%s</comment><info>".</info>',
            $totalCount,
            $this->metaLib->getName()
        ));

        $errors = [];

        $progressBar = new ProgressBar($output, $totalCount);
        $progressBar->start();

        foreach ($remoteIds as $mediaMonkeyId) {
            $progressBar->advance();
            $mediaMonkeyData = $this
                ->mediaMonkeyDatabaseManager
                ->fetchSongData($mediaMonkeyId)
            ;

            // Check if file has the expected path
            if (mb_strpos($mediaMonkeyData['file_path_name'], $this->metaLib->getRootPath()) !== 0) {
                $errors[] = sprintf(
                    'Skipped MediaMonkey song %s: unexpected file path "%s".',
                    $mediaMonkeyId,
                    $mediaMonkeyData['file_path_name']
                );

                continue;
            }

            $data = [
                'file_path_name' => ltrim(mb_substr($mediaMonkeyData['file_path_name'], mb_strlen($this->metaLib->getRootPath())), '\/'),
                'external_id' => $mediaMonkeyId,
                'is_synthetic' => false,

                'added_date' => $mediaMonkeyData['added_date'],
                'album' => $mediaMonkeyData['album'],
                'bitrate' => $mediaMonkeyData['bitrate'],
                'bpm' => $mediaMonkeyData['bpm'],
                'date' => $mediaMonkeyData['date'],
                'disc_number' => $mediaMonkeyData['disc_number'],
                'initial_key' => $mediaMonkeyData['initial_key'],
                'music_brainz_id' => null,
                'publisher' => $mediaMonkeyData['publisher'],
                'rating' => $mediaMonkeyData['rating'],
                'sampling_frequency' => $mediaMonkeyData['sampling_frequency'],
                'title' => $mediaMonkeyData['title'],
                'track_number' => $mediaMonkeyData['track_number'],
                'year' => $mediaMonkeyData['year'],

                'artists' => array_map(function (string $title): array {
                    return [
                        'title' => $title,
                        'music_brainz_id' => null,
                    ];
                }, $mediaMonkeyData['artists']),
                'genres' => $mediaMonkeyData['genres'],
                'play_dates' => array_map(
                    function (\DateTime $date): array {
                        return [
                            'date' => $date,
                            'prec' => 0,
                            'count' => 1,
                        ];
                    },
                    array_filter(
                        $this->mediaMonkeyDatabaseManager->fetchSongPlayDates($mediaMonkeyId),
                        function (\DateTime $date) {
                            // todo Whitelist (excluding) 2019-08-17 Aug 17:48 and 2019-08-19 10:16 (Scrobbler downtime)
                            return $date < $this->lastFmStartDate;
                        }
                    )
                ),
            ];

            // Skips
            $newSkips = $mediaMonkeyData['skip_count'] - $this->getSkipCount($mediaMonkeyId);
            if ($newSkips > 0) {
                $data['new_skip_dates'][] = [
                    'date' => new \DateTime(),
                    'prec' => 0, // todo Fix precision
                    'count' => $newSkips,
                ];
            }

            $this->metaFileWriter->writeMetaFile($this->metaLib, $data);

            $this->entityManager->clear(Song::class);
            $this->entityManager->clear(File::class);
            $this->entityManager->clear(MetaFile::class);
            $this->entityManager->clear(MetaFileArtist::class);
            $this->entityManager->clear(MetaFileGenre::class);
            $this->entityManager->clear(MetaFileTouch::class);
        }

        $progressBar->finish();
        $output->writeln('');

        // Mark unknown songs an deleted
        $qb = $this
            ->entityManager
            ->createQueryBuilder()
        ;
        $deletedCount = $qb
            ->update(MetaFile::class, 'mf')
            ->set('mf.isDeleted', ':isDeleted')
            ->set('mf.deletionDate', ':deletionDate')
            ->where('mf.isDeleted = 0')
            ->andWhere('mf.externalId NOT IN (:ids)')
            ->andWhere('mf.metaLib = :metaLib')
            ->setParameters([
                'ids' => $remoteIds,
                'isDeleted' => true,
                'deletionDate' => new \DateTime(),
                'metaLib' => $this->metaLib,
            ])
            ->getQuery()
            ->execute()
        ;

        $output->writeln(sprintf('Created %d new songs.', $this->metaFileWriter->getMetaFileCreatedCount()));
        $output->writeln(sprintf('Marked %d songs as deleted.', $deletedCount));

        if (!empty($errors)) {
            $output->writeln(implode("\n", $errors));
        }

        $output->writeln('Done. 🎉');
    }

    private function assertDatabaseIsValid()
    {
        // Check if any song exists in local database
        $stmtCount = $this
            ->entityManager
            ->getConnection()
            ->prepare('SELECT COUNT(1) FROM meta_file WHERE meta_lib_id = :meta_lib_id')
        ;
        $stmtCount->execute([
            'meta_lib_id' => $this->metaLib->getId(),
        ]);

        if ($stmtCount->fetch(FetchMode::COLUMN) == 0) {
            return;
        }

        // todo Currently a meta file has no last_played_date
        //// Check last added date and last played date
        //$stmtMediaMonkey = $this
        //    ->mediaMonkeyDatabase
        //    ->getConnection()
        //    ->prepare('
        //        SELECT
        //            MAX(datetime(julianday(Songs.DateAdded) + julianday("1899-12-30"), "localtime")),
        //            MAX(
        //                CASE
        //                    WHEN LastTimePlayed > 0
        //                    THEN datetime(julianday(Songs.LastTimePlayed) + julianday("1899-12-30"), "localtime")
        //                    ELSE NULL
        //                END
        //            )
        //
        //        FROM Songs
        //    ')
        //;
        //$stmtMediaMonkey->execute();
        //list($maxAddedDateMediaMonkey, $maxLastPlayedDateMediaMonkey) = $stmtMediaMonkey->fetch(FetchMode::NUMERIC);
        //$maxAddedDateMediaMonkey = new \DateTime($maxAddedDateMediaMonkey);
        //$maxLastPlayedDateMediaMonkey = new \DateTime($maxLastPlayedDateMediaMonkey);
        //
        //$stmtLocal = $this
        //    ->entityManager
        //    ->getConnection()
        //    ->prepare('
        //        SELECT
        //            MAX(added_date),
        //            MAX(last_played_date)
        //
        //        FROM media_monkey_song
        //    ')
        //;
        //$stmtLocal->execute();
        //list($maxAddedDateLocal, $maxLastPlayedDateLocal) = $stmtLocal->fetch(FetchMode::NUMERIC);
        //$maxAddedDateLocal = new \DateTime($maxAddedDateLocal);
        //$maxLastPlayedDateLocal = new \DateTime($maxLastPlayedDateLocal);
        //
        //if ($maxAddedDateLocal > $maxAddedDateMediaMonkey) {
        //    throw new \RuntimeException(sprintf(
        //        'Max added_date is greater in local DB (%s) than in MediaMonkey DB (%s)',
        //        $maxAddedDateLocal->format('Y-m-d H:i:s'),
        //        $maxAddedDateMediaMonkey->format('Y-m-d H:i:s')
        //    ));
        //}
        //
        //if ($maxLastPlayedDateLocal > $maxLastPlayedDateMediaMonkey) {
        //    throw new \RuntimeException(sprintf(
        //        'Max last_played_date is greater in local DB (%s) than in MediaMonkey DB (%s)',
        //        $maxLastPlayedDateLocal->format('Y-m-d H:i:s'),
        //        $maxLastPlayedDateMediaMonkey->format('Y-m-d H:i:s')
        //    ));
        //}
    }

    private function getSkipCount(int $mediaMonkeyId): int
    {
        $stmt = $this->entityManager->getConnection()->prepare("
            SELECT IFNULL(SUM(mft.count), 0)
    
            FROM meta_file mf
            
            INNER JOIN meta_file_touch mft
            ON mft.meta_file_id = mf.id
            
            WHERE
                mf.meta_lib_id = :meta_lib_id
                AND mf.external_id = :media_monkey_id
                AND mft.type = :type
        ");
        $stmt->execute([
            'meta_lib_id' => $this->metaLib->getId(),
            'media_monkey_id' => $mediaMonkeyId,
            'type' => MetaFileTouch::TYPE_SKIP,
        ]);

        return $stmt->fetchColumn();
    }
}
