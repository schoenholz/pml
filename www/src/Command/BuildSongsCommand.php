<?php

namespace App\Command;

use App\Entity\MediaMonkeySong;
use App\Entity\Song;
use App\Entity\SongArtist;
use App\Entity\SongGenre;
use App\Entity\SongTouch;
use App\Repository\MediaMonkeySongRepository;
use App\Repository\SongRepository;
use App\SongTitleNormalizer;
use App\Task\AbstractPostProcessTask;
use App\Task\Song\PostProcess\UpdateDeletionDateTask;
use App\Task\Song\PostProcess\UpdateTouchCountTask;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildSongsCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SongTitleNormalizer
     */
    private $songTitleNormalizer;

    /**
     * @var \App\Task\AbstractPostProcessTask[]
     */
    private $postProcessTasks = [];

    /**
     * @var int
     */
    private $importedPlaysSum;

    /**
     * @var int
     */
    private $importedSkipsSum;

    public function __construct(
        EntityManagerInterface $entityManager,
        SongTitleNormalizer $songTitleNormalizer,
        UpdateTouchCountTask $updateTouchCountTask,
        UpdateDeletionDateTask $updateDeletionDateTask
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->songTitleNormalizer = $songTitleNormalizer;
        $this->postProcessTasks[] = $updateTouchCountTask;
        $this->postProcessTasks[] = $updateDeletionDateTask;
    }

    protected function configure()
    {
        $this
            ->setName('app:songs:build')
            ->setDescription('Build song library.')
            ->addOption('post-process', 'p', InputOption::VALUE_NONE, 'Post process only.')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->importedPlaysSum = 0;
        $this->importedSkipsSum = 0;

        if (!$input->getOption('post-process')) {
            $this->buildSongs($output);
        }

        $this->postProcess($output);
    }

    private function buildSongs(OutputInterface $output)
    {
        $output->writeln('<info>Building songs.</info>');

        $mediaMonkeySongIds = $this->getMediaMonkeySongIds();
        $totalCount = count($mediaMonkeySongIds);
        $createdSongsCount = 0;

        /** @var MediaMonkeySongRepository $mediaMonkeySongRepo */
        $mediaMonkeySongRepo = $this->entityManager->getRepository(MediaMonkeySong::class);
        /** @var SongRepository $songRepo */
        $songRepo = $this->entityManager->getRepository(Song::class);

        $progressBar = new ProgressBar($output, $totalCount);
        $progressBar->start();

        foreach ($mediaMonkeySongIds as $mediaMonkeySongId) {
            $progressBar->advance();
            $mediaMonkeySong = $mediaMonkeySongRepo->findOneBy([
                'id' => $mediaMonkeySongId,
            ]);

            if (!$mediaMonkeySong) {
                throw new \RuntimeException();
            }

            $song = $songRepo->findOneBy([
                'mediaMonkeyId' => $mediaMonkeySong->getMediaMonkeyId(),
            ]);

            if (!$song) {
                $createdSongsCount ++;
                $song = new Song();
                $song
                    ->setMediaMonkeyId($mediaMonkeySong->getMediaMonkeyId())
                    ->setTouchCount(0)
                ;
            }

            $this->updateSong($song, $mediaMonkeySong);

            $this->entityManager->flush();

            $this->entityManager->clear(MediaMonkeySong::class);
            $this->entityManager->clear(Song::class);
            $this->entityManager->clear(SongArtist::class);
            $this->entityManager->clear(SongGenre::class);
            $this->entityManager->clear(SongTouch::class);
        }

        // todo set all songs to "deleted" if not covered by last import with deletion date

        $progressBar->finish();
        $output->writeln('');

        $output->writeln(sprintf('Created <comment>%d</comment> new songs.', $createdSongsCount));
        $output->writeln(sprintf('Found <comment>%d</comment> new playbacks.', $this->importedPlaysSum));
        $output->writeln(sprintf('Found <comment>%d</comment> new skips.', $this->importedSkipsSum));
    }

    private function postProcess(OutputInterface $output)
    {
        $output->writeln('<info>Post processing songs.</info>');

        $progressBar = new ProgressBar($output, count($this->postProcessTasks));
        $progressBar->start();

        foreach ($this->postProcessTasks as $task) {
            $task->run();
            $progressBar->advance();
        }

        $progressBar->finish();
        $output->writeln('');
    }

    private function updateSong(
        Song $song,
        MediaMonkeySong $mediaMonkeySong
    ) {
        $song
            ->setAddedDate($mediaMonkeySong->getAddedDate())
            ->setAlbum($mediaMonkeySong->getAlbum())
            ->setBitrate($mediaMonkeySong->getBitrate())
            ->setBpm($mediaMonkeySong->getBpm())
            ->setDate($mediaMonkeySong->getDate())
            ->setDeletionDate($mediaMonkeySong->getDeletionDate())
            ->setDiscNumber($mediaMonkeySong->getDiscNumber())
            ->setFilePathName($mediaMonkeySong->getFilePathName())
            ->setInitialKey($mediaMonkeySong->getInitialKey())
            ->setIsDeleted($mediaMonkeySong->getIsDeleted())
            ->setLastImportDate(new \DateTime())
            ->setPublisher($mediaMonkeySong->getPublisher())
            ->setSamplingFrequency($mediaMonkeySong->getSamplingFrequency())
            ->setTitle($mediaMonkeySong->getTitle())
            ->setTitleNormalized($this->songTitleNormalizer->normalize($mediaMonkeySong->getTitle()))
            ->setTrackNumber($mediaMonkeySong->getTrackNumber())
            ->setYear($mediaMonkeySong->getYear())
        ;

        if ($song->getPlayCount() < $mediaMonkeySong->getPlayCount()) {
            $song->setLastPlayDate($mediaMonkeySong->getLastPlayedDate() ?? new \DateTime());

            $songTouchPlayed = new SongTouch();
            $songTouchPlayed
                ->setSong($song)
                ->setDate($song->getLastPlayDate())
                ->setType(SongTouch::TYPE_PLAY)
                ->setCount($mediaMonkeySong->getPlayCount() - $song->getPlayCount())
            ;
            $this->entityManager->persist($songTouchPlayed);

            $this->importedPlaysSum += $songTouchPlayed->getCount();
        } elseif (
            $song->getPlayCount() > 0
            && !$song->getLastPlayDate()
        ) {
            $song->setLastPlayDate($mediaMonkeySong->getLastPlayedDate() ?? new \DateTime());
        }

        $song->setPlayCount($mediaMonkeySong->getPlayCount());

        if ($song->getSkipCount() < $mediaMonkeySong->getSkipCount()) {
            $song->setLastSkipDate(new \DateTime());

            $songTouchSkipped = new SongTouch();
            $songTouchSkipped
                ->setSong($song)
                ->setDate($song->getLastSkipDate())
                ->setType(SongTouch::TYPE_SKIP)
                ->setCount($mediaMonkeySong->getSkipCount() - $song->getSkipCount())
            ;
            $this->entityManager->persist($songTouchSkipped);

            $this->importedSkipsSum += $songTouchSkipped->getCount();
        } elseif (
            $song->getSkipCount() > 0
            && !$song->getLastSkipDate()
        ) {
            $song->setLastSkipDate(new \DateTime());
        }

        $song->setSkipCount($mediaMonkeySong->getSkipCount());

        if ($mediaMonkeySong->getPlayCount() > 0) {
            $song->setFirstPlayDate(min(
                $mediaMonkeySong->getFirstPlayedDate() ?? new \DateTime(),
                $song->getFirstPlayDate() ?? new \DateTime(),
                $mediaMonkeySong->getLastPlayedDate() ?? new \DateTime()
            ));
        }

        if (
            $mediaMonkeySong->getSkipCount() > 0
            && $song->getFirstSkipDate() === null
        ) {
            $song->setFirstSkipDate(new \DateTime());
        }

        if ($song->getFirstImportDate() === null) {
            $song->setFirstImportDate(new \DateTime());
        }

        if ($song->getRating() != $mediaMonkeySong->getRating()) {
            $song->setRatingDate(new \DateTime());
        }

        $song->setRating($mediaMonkeySong->getRating());

        if (
            $mediaMonkeySong->getRating() !== null
            && (
                $song->getBestRating() === null
                || $song->getRating() > $song->getBestRating()
            )
        ) {
            // todo Create rating history
            $song->setBestRating($mediaMonkeySong->getRating());
        }

        if ($song->getFirstPlayDate()) {
            if ($song->getFirstSkipDate()) {
                $song->setFirstTouchDate(min(
                    $song->getFirstPlayDate(),
                    $song->getFirstSkipDate()
                ));
            } else {
                $song->setFirstTouchDate($song->getFirstPlayDate());
            }
        } elseif ($song->getFirstSkipDate()) {
            $song->setFirstTouchDate($song->getFirstSkipDate());
        }

        if ($song->getLastPlayDate()) {
            if ($song->getLastSkipDate()) {
                $song->setLastTouchDate(max(
                    $song->getLastPlayDate(),
                    $song->getLastSkipDate()
                ));
            } else {
                $song->setLastTouchDate($song->getLastPlayDate());
            }
        } elseif ($song->getLastSkipDate()) {
            $song->setLastTouchDate($song->getLastSkipDate());
        }

        /*
         * Artist
         */
        $futureArtists = $mediaMonkeySong->getArtist();
        $currentArtists = $song->getSongArtists();

        foreach ($currentArtists as $currentArtist) {
            $futureArtistIndex = array_search($currentArtist->getTitle(), $futureArtists);

            if ($futureArtistIndex !== false) {
                unset($futureArtists[$futureArtistIndex]);
            } else {
                $this->entityManager->remove($currentArtist);
            }
        }

        foreach ($futureArtists as $futureArtist) {
            $songArtist = new SongArtist();
            $songArtist
                ->setSong($song)
                ->setTitle($futureArtist)
            ;
            $this->entityManager->persist($songArtist);
        }

        /*
         * Genre
         */
        $futureGenres = $mediaMonkeySong->getGenre();
        $currentGenres = $song->getSongGenres();

        foreach ($currentGenres as $currentGenre) {
            $futureGenreIndex = array_search($currentGenre->getTitle(), $futureGenres);

            if ($futureGenreIndex !== false) {
                unset($futureGenres[$futureGenreIndex]);
            } else {
                $this->entityManager->remove($currentGenre);
            }
        }

        foreach ($futureGenres as $futureGenre) {
            $songGenre = new SongGenre();
            $songGenre
                ->setSong($song)
                ->setTitle($futureGenre)
            ;
            $this->entityManager->persist($songGenre);
        }

        $this->entityManager->persist($song);
    }

    private function getMediaMonkeySongIds(): array
    {
        $stmt = $this
            ->entityManager
            ->getConnection()
            ->prepare('
                SELECT id
                FROM media_monkey_song
            ')
        ;
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
