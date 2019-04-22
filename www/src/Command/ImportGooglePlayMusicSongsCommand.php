<?php

namespace App\Command;

use App\ArtistSplitter;
use App\Entity\File;
use App\Entity\MetaFile;
use App\Entity\MetaFileArtist;
use App\Entity\MetaFileTouch;
use App\Entity\Song;
use App\MetaFileWriter;
use App\Repository\MetaLibRepository;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ImportGooglePlayMusicSongsCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

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
            ->setName('app:songs:import:google_play_music')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $metaLib = $this->metaLibRepository->requireOneBy('name', 'Google Play Music moggerztinef@gmail.com');

        $dir = $this->container->getParameter('kernel.root_dir') . '/../var/google_takeout/Tracks';

        $fi = new Finder();
        $fi
            ->files()
            ->name('*.csv')
            ->in($dir)
            ->depth(0)
        ;

        $tracks = [];
        foreach ($fi as $fileInfo) {
            $track = $this->readFile($fileInfo);

            if ($track['play_count'] == 0) {
                continue;
            }

            if (array_key_exists($track['id'], $tracks)) {
                $tracks[$track['id']]['play_count'] = max($tracks[$track['id']]['play_count'], $track['play_count']);
            } else {
                $tracks[$track['id']] = $track;
            }
        }

        $totalCount = count($tracks);

        $output->writeln(sprintf(
            '<info>Importing </info><comment>%d</comment><info> songs from Google Play Music Takeout data into meta lib "</info><comment>%s</comment><info>".</info>',
            $totalCount,
            $metaLib->getName()
        ));

        $progressBar = new ProgressBar($output, $totalCount);
        $progressBar->start();

        foreach ($tracks as $track) {
            $progressBar->advance();

            $data = [
                'file_path_name' => $track['id'],
                'external_id' => $track['id'],
                'is_synthetic' => true,

                'added_date' => null,
                'album' => $track['album'],
                'bitrate' => null,
                'bpm' => null,
                'date' => null,
                'disc_number' => null,
                'initial_key' => null,
                'music_brainz_id' => null,
                'publisher' => null,
                'rating' => null,
                'sampling_frequency' => null,
                'title' => $track['title'],
                'track_number' => null,
                'year' => null,

                'artists' => $track['artists'],
                'genres' => [],
                'play_dates' => [
                    [
                        'date' => null,
                        'prec' => 0,
                        'count' => ceil(($track['play_count'] - $this->getLastFmPlayCount($track['id'])) / 2),
                    ]
                ],
            ];

            $this->metaFileWriter->writeMetaFile($metaLib, $data);

            $this->entityManager->clear(Song::class);
            $this->entityManager->clear(File::class);
            $this->entityManager->clear(MetaFile::class);
            $this->entityManager->clear(MetaFileArtist::class);
            //$this->entityManager->clear(MetaFileGenre::class);
            $this->entityManager->clear(MetaFileTouch::class);
        }

        $progressBar->finish();
        $output->writeln('');

        $output->writeln(sprintf('Created %d new songs.', $this->metaFileWriter->getMetaFileCreatedCount()));
        $output->writeln('Done. 🎉');
    }

    private function readFile(SplFileInfo $fileInfo): array
    {
        $csv = array_map('str_getcsv', file($fileInfo->getPathname()));

        if (count($csv) !== 2) {
            throw new \Exception(sprintf('File "%s" contain %d lines instead of 2', $fileInfo->getRelativePathname(), count($csv)));
        }

        $csv = array_map([$this, 'decodeRow'], $csv);
        $data = array_combine($csv[0], $csv[1]);

        $artists = array_map(function (string $artistTitle) {
            return [
                'title' => $artistTitle,
                'music_brainz_id' => null,
            ];
        }, $this->artistSplitter->split($data['Artist'], $data['Title']));

        return [
            'id' => sprintf('gpm:-+-%s-+-%s-+-%s', $data['Artist'], $data['Album'], $data['Title']),
            'title' => $data['Title'],
            'album' => $data['Album'],
            'artists' => $artists,
            'play_count' => $data['Play Count'],
        ];

    }

    private function decodeRow(array $row): array
    {
        return array_map([$this, 'decodeStr'], $row);
    }

    private function decodeStr(string $str): string
    {
        return html_entity_decode($str, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function getLastFmPlayCount(string $id): int
    {
        $stmt = $this->entityManager->getConnection()->prepare("
            SELECT
                IFNULL(SUM(mft_lfm.count), 0)

            FROM meta_file mf_gpm
            
            INNER JOIN file f_gpm
            ON f_gpm.id = mf_gpm.file_id
            
            INNER JOIN song s
            ON s.id = f_gpm.song_id
            
            INNER JOIN file f_lfm
            ON f_lfm.song_id = s.id
            AND f_lfm.id <> f_gpm.id
            
            INNER JOIN meta_file mf_lfm
            ON mf_lfm.file_id = f_lfm.id
            AND mf_lfm.title = mf_gpm.title
            AND mf_lfm.meta_lib_id = (SELECT id FROM meta_lib WHERE name = 'last.fm User t1n3f') 
            
            INNER JOIN meta_file_touch mft_lfm
            ON mft_lfm.meta_file_id = mf_lfm.id
            AND mft_lfm.type = 'play'
            AND mft_lfm.date IS NOT NULL
            AND mft_lfm.date < '2019-02-22' -- date of Google Takeout
            
            
            WHERE
                mf_gpm.meta_lib_id = (SELECT id FROM meta_lib WHERE name = 'Google Play Music moggerztinef@gmail.com') 
                AND mf_gpm.external_id = :id
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        return $stmt->fetch(FetchMode::COLUMN);
    }
}
