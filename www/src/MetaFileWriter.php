<?php

namespace App;

use App\Entity\File;
use App\Entity\MetaFile;
use App\Entity\MetaFileArtist;
use App\Entity\MetaFileGenre;
use App\Entity\MetaFileTouch;
use App\Entity\MetaLib;
use App\Entity\Song;
use App\Repository\FileRepository;
use App\Repository\MetaFileRepository;
use Doctrine\ORM\EntityManagerInterface;

class MetaFileWriter
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MetaFileRepository
     */
    private $metaFileRepository;

    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var SongTitleNormalizer
     */
    private $songTitleNormalizer;

    /**
     * @var ArtistTitleNormalizer
     */
    private $artistTitleNormalizer;

    /**
     * @var int
     */
    private $metaFileCreatedCount = 0;

    public function __construct(
        EntityManagerInterface $entityManager,
        MetaFileRepository $metaFileRepository,
        FileRepository $fileRepository,
        SongTitleNormalizer $songTitleNormalizer,
        ArtistTitleNormalizer $artistTitleNormalizer
    ) {
        $this->entityManager = $entityManager;
        $this->metaFileRepository = $metaFileRepository;
        $this->fileRepository = $fileRepository;
        $this->songTitleNormalizer = $songTitleNormalizer;
        $this->artistTitleNormalizer = $artistTitleNormalizer;
    }

    public function getMetaFileCreatedCount(): int
    {
        return $this->metaFileCreatedCount;
    }

    public function writeMetaFile(
        MetaLib $metaLib,
        array $data
    ): int {
        if (empty($data['title_normalized'])) {
            $data['title_normalized'] = $this->songTitleNormalizer->normalize($data['title']);
        }

        // Find the File
        $file = $this->fileRepository->findOneBy([
            'filePathName' => $data['file_path_name'],
        ]);

        if (!$file) {
            $song = new Song();
            $song
                ->setAddedDate($data['added_date'])
                ->setBestRatingScoreDate(new \DateTime())
                ->setBestLastTouchDateScoreDate(new \DateTime())
                ->setBestPlayCountScoreDate(new \DateTime())
                ->setBestPlayedPerTouchScoreDate(new \DateTime())
            ;
            $file = new File();
            $file
                ->setSong($song)
                ->setSongRelation(File::SONG_RELATION_PRIMARY)
                ->setFilePathName($data['file_path_name'])
            ;

            $this->entityManager->persist($song);
            $this->entityManager->persist($file);

            $this->metaFileCreatedCount++;
        }

        $file->setIsSynthetic($data['is_synthetic']);

        // Find the MetaFile
        $metaFile = $this->metaFileRepository->findOneBy([
            'metaLib' => $metaLib,
            'externalId' => $data['external_id'],
        ]);

        if (!$metaFile) {
            $metaFile = new MetaFile();
            $metaFile
                ->setMetaLib($metaLib)
                ->setExternalId($data['external_id'])
                ->setFirstImportDate(new \DateTime())
            ;
            $this->entityManager->persist($metaFile);
        }

        // Update data
        $metaFile
            ->setFile($file)
            ->setLastImportDate(new \DateTime())
            ->setAddedDate($data['added_date'])
            ->setIsDeleted(isset($data['is_deleted']) ? $data['is_deleted'] : false)
            ->setDeletionDate(isset($data['deletion_date']) ? $data['deletion_date'] : null)

            ->setAlbum($data['album'])
            ->setBitrate($data['bitrate'])
            ->setBpm($data['bpm'])
            ->setDate($data['date'])
            ->setDiscNumber($data['disc_number'])
            ->setInitialKey($data['initial_key'])
            ->setIsDeleted(false)
            ->setMusicBrainzId($data['music_brainz_id'])
            ->setPublisher($data['publisher'])
            ->setRating($data['rating'])
            ->setSamplingFrequency($data['sampling_frequency'])
            ->setTitle($data['title'])
            ->setTitleNormalized($this->songTitleNormalizer->normalize($data['title']))
            ->setTrackNumber($data['track_number'])
            ->setYear($data['year'])
        ;

        $this->writeMetaFileArtists(MetaFileArtist::RELATION_ARTISTS, $metaFile, $data['artists']);
        $this->writeMetaFileGenres($metaFile, $data['genres']);

        if (array_key_exists('play_dates', $data)) {
            $this->writeMetaFileTouches(MetaFileTouch::TYPE_PLAY, $metaFile, $data['play_dates']);
        }

        if (array_key_exists('skip_dates', $data)) {
            $this->writeMetaFileTouches(MetaFileTouch::TYPE_SKIP, $metaFile, $data['skip_dates']);
        }

        if (array_key_exists('new_skip_dates', $data)) {
            $this->addMetaFileTouches(MetaFileTouch::TYPE_SKIP, $metaFile, $data['new_skip_dates']);
        }

        // todo Rating history in MetaFileRating

        $this->entityManager->flush();

        return $metaFile->getId();
    }

    private function writeMetaFileArtists(
        string $relation,
        MetaFile $metaFile,
        array $artists
    ) {
        /* @var MetaFileArtist[] $currentArtists */
        $currentArtists = $metaFile->getMetaFileArtists()->filter(function (MetaFileArtist $metaFileArtist) use ($relation) {
            return $metaFileArtist->getRelation() == $relation;
        });
        $futureArtists = $artists;

        foreach ($currentArtists as $currentArtist) {
            // Find existing artists and update them
            foreach ($futureArtists as $k => $futureArtist) {
                if ($futureArtist['title'] == $currentArtist->getTitle()) {
                    $currentArtist
                        ->setTitleNormalized($this->artistTitleNormalizer->normalize($currentArtist->getTitle()))
                        ->setMusicBrainzId($futureArtist['music_brainz_id'])
                    ;

                    unset($futureArtists[$k]);

                    continue 2;
                }
            }

            $this->entityManager->remove($currentArtist);
        }

        // Flush entity manager to actually remove artists. Case insensitive constraints may cause problems.
        $this->entityManager->flush();

        $this->addMetaFileArtists($relation, $metaFile, $futureArtists);
    }

    private function addMetaFileArtists(
        string $relation,
        MetaFile $metaFile,
        array $artists
    ) {
        foreach ($artists as $artist) {
            $metaFileArtist = new MetaFileArtist();
            $metaFileArtist
                ->setMetaFile($metaFile)
                ->setRelation($relation)
                ->setTitle($artist['title'])
                ->setMusicBrainzId($artist['music_brainz_id'])
                ->setTitleNormalized($this->artistTitleNormalizer->normalize($artist['title']))
            ;
            $this->entityManager->persist($metaFileArtist);
        }
    }

    private function writeMetaFileGenres(
        MetaFile $metaFile,
        array $genres
    ) {
        $futureGenres = $genres;
        $currentGenres = $metaFile->getMetaFileGenres();

        foreach ($currentGenres as $currentGenre) {
            $futureGenreIndex = array_search($currentGenre->getTitle(), $futureGenres);

            if ($futureGenreIndex !== false) {
                unset($futureGenres[$futureGenreIndex]);
            } else {
                $this->entityManager->remove($currentGenre);
            }
        }

        $this->addMetaFileGenres($metaFile, $futureGenres);
    }

    public function addMetaFileGenres(
        MetaFile $metaFile,
        array $genres
    ) {
        foreach ($genres as $genre) {
            $metaFileGenre = new MetaFileGenre();
            $metaFileGenre
                ->setMetaFile($metaFile)
                ->setTitle($genre)
            ;
            $this->entityManager->persist($metaFileGenre);
        }
    }

    private function writeMetaFileTouches(
        string $type,
        MetaFile $metaFile,
        array $touches
    ) {
        $futureTouches = $touches;
        /* @var MetaFileTouch[] $currentTouches */
        $currentTouches = $metaFile->getMetaFileTouches()->filter(function (MetaFileTouch $metaFileTouch) use ($type) {
            return $metaFileTouch->getType() === $type;
        });

        foreach ($currentTouches as $currentTouch) {
            foreach ($futureTouches as $k => $futureTouch) {
                if (
                    $currentTouch->getDate() == $futureTouch['date']
                    && $currentTouch->getCount() == $futureTouch['count']
                    && $currentTouch->getPrec() == $futureTouch['prec']
                ) {
                    unset($futureTouches[$k]);

                    continue 2;
                }
            }

            $this->entityManager->remove($currentTouch);
        }

        $this->addMetaFileTouches($type, $metaFile, $futureTouches);
    }

    private function addMetaFileTouches(
        string $type,
        MetaFile $metaFile,
        array $touches
    ) {
        foreach ($touches as $touch) {
            $metaFileTouch = new MetaFileTouch();
            $metaFileTouch
                ->setMetaFile($metaFile)
                ->setType($type)
                ->setDate($touch['date'])
                ->setPrec($touch['prec'])
                ->setCount($touch['count'])
            ;
            $this->entityManager->persist($metaFileTouch);
        }
    }
}
