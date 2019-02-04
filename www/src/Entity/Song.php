<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SongRepository")
 */
class Song
{
    const MIN_RATING_CONSIDERED_RELEVANT = 61;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true, unique=true)
     */
    private $mediaMonkeyId;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $filePathName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titleNormalized;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $album;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $publisher;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bitrate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $samplingFrequency;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $ratingDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bestRating;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bpm;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $initialKey;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $discNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $trackNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $touchCount;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstTouchDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastTouchDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $playCount;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstPlayDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastPlayDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $skipCount;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstSkipDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastSkipDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $addedDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $firstImportDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastImportDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletionDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SongArtist", mappedBy="song", orphanRemoval=true)
     */
    private $songArtists;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SongGenre", mappedBy="song", orphanRemoval=true)
     */
    private $songGenres;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SongTouch", mappedBy="song", orphanRemoval=true)
     */
    private $songTouches;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WorkHasSong", mappedBy="song")
     */
    private $workHasSongs;

    public function __construct()
    {
        $this->songArtists = new ArrayCollection();
        $this->songGenres = new ArrayCollection();
        $this->songTouches = new ArrayCollection();
        $this->workHasSongs = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBitrate(): ?int
    {
        return $this->bitrate;
    }

    public function setBitrate(?int $bitrate): self
    {
        $this->bitrate = $bitrate;

        return $this;
    }

    public function getPlayCount(): ?int
    {
        return $this->playCount;
    }

    public function setPlayCount(int $playCount): self
    {
        $this->playCount = $playCount;

        return $this;
    }

    public function getSkipCount(): ?int
    {
        return $this->skipCount;
    }

    public function setSkipCount(int $skipCount): self
    {
        $this->skipCount = $skipCount;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getAlbum(): ?string
    {
        return $this->album;
    }

    public function setAlbum(?string $album): self
    {
        $this->album = $album;

        return $this;
    }

    /**
     * @return Collection|SongArtist[]
     */
    public function getSongArtists(): Collection
    {
        return $this->songArtists;
    }

    public function addSongArtist(SongArtist $songArtist): self
    {
        if (!$this->songArtists->contains($songArtist)) {
            $this->songArtists[] = $songArtist;
            $songArtist->setSong($this);
        }

        return $this;
    }

    public function removeSongArtist(SongArtist $songArtist): self
    {
        if ($this->songArtists->contains($songArtist)) {
            $this->songArtists->removeElement($songArtist);
            // set the owning side to null (unless already changed)
            if ($songArtist->getSong() === $this) {
                $songArtist->setSong(null);
            }
        }

        return $this;
    }

    public function getMediaMonkeyId(): ?int
    {
        return $this->mediaMonkeyId;
    }

    public function setMediaMonkeyId(int $mediaMonkeyId): self
    {
        $this->mediaMonkeyId = $mediaMonkeyId;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getFilePathName(): ?string
    {
        return $this->filePathName;
    }

    public function setFilePathName(?string $filePathName): self
    {
        $this->filePathName = $filePathName;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getBpm(): ?int
    {
        return $this->bpm;
    }

    public function setBpm(?int $bpm): self
    {
        $this->bpm = $bpm;

        return $this;
    }

    public function getInitialKey(): ?string
    {
        return $this->initialKey;
    }

    public function setInitialKey(?string $initialKey): self
    {
        $this->initialKey = $initialKey;

        return $this;
    }

    public function getDiscNumber(): ?int
    {
        return $this->discNumber;
    }

    public function setDiscNumber(?int $discNumber): self
    {
        $this->discNumber = $discNumber;

        return $this;
    }

    public function getTrackNumber(): ?int
    {
        return $this->trackNumber;
    }

    public function setTrackNumber(?int $trackNumber): self
    {
        $this->trackNumber = $trackNumber;

        return $this;
    }

    public function getSamplingFrequency(): ?int
    {
        return $this->samplingFrequency;
    }

    public function setSamplingFrequency(?int $samplingFrequency): self
    {
        $this->samplingFrequency = $samplingFrequency;

        return $this;
    }

    public function getFirstImportDate(): ?\DateTimeInterface
    {
        return $this->firstImportDate;
    }

    public function setFirstImportDate(?\DateTimeInterface $firstImportDate): self
    {
        $this->firstImportDate = $firstImportDate;

        return $this;
    }

    public function getLastImportDate(): ?\DateTimeInterface
    {
        return $this->lastImportDate;
    }

    public function setLastImportDate(?\DateTimeInterface $lastImportDate): self
    {
        $this->lastImportDate = $lastImportDate;

        return $this;
    }

    /**
     * @return Collection|SongGenre[]
     */
    public function getSongGenres(): Collection
    {
        return $this->songGenres;
    }

    public function addSongGenre(SongGenre $songGenre): self
    {
        if (!$this->songGenres->contains($songGenre)) {
            $this->songGenres[] = $songGenre;
            $songGenre->setSong($this);
        }

        return $this;
    }

    public function removeSongGenre(SongGenre $songGenre): self
    {
        if ($this->songGenres->contains($songGenre)) {
            $this->songGenres->removeElement($songGenre);
            // set the owning side to null (unless already changed)
            if ($songGenre->getSong() === $this) {
                $songGenre->setSong(null);
            }
        }

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getDeletionDate(): ?\DateTimeInterface
    {
        return $this->deletionDate;
    }

    public function setDeletionDate(?\DateTimeInterface $deletionDate): self
    {
        $this->deletionDate = $deletionDate;

        return $this;
    }

    public function getRatingDate(): ?\DateTimeInterface
    {
        return $this->ratingDate;
    }

    public function setRatingDate(?\DateTimeInterface $ratingDate): self
    {
        $this->ratingDate = $ratingDate;

        return $this;
    }

    public function getFirstTouchDate(): ?\DateTimeInterface
    {
        return $this->firstTouchDate;
    }

    public function setFirstTouchDate(?\DateTimeInterface $firstTouchDate): self
    {
        $this->firstTouchDate = $firstTouchDate;

        return $this;
    }

    public function getLastTouchDate(): ?\DateTimeInterface
    {
        return $this->lastTouchDate;
    }

    public function setLastTouchDate(?\DateTimeInterface $lastTouchDate): self
    {
        $this->lastTouchDate = $lastTouchDate;

        return $this;
    }

    public function getTouchCount(): ?int
    {
        return $this->touchCount;
    }

    public function setTouchCount(int $touchCount): self
    {
        $this->touchCount = $touchCount;

        return $this;
    }

    public function getAddedDate(): ?\DateTimeInterface
    {
        return $this->addedDate;
    }

    public function setAddedDate(\DateTimeInterface $addedDate): self
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    public function getFirstPlayDate(): ?\DateTimeInterface
    {
        return $this->firstPlayDate;
    }

    public function setFirstPlayDate(?\DateTimeInterface $firstPlayDate): self
    {
        $this->firstPlayDate = $firstPlayDate;

        return $this;
    }

    public function getLastPlayDate(): ?\DateTimeInterface
    {
        return $this->lastPlayDate;
    }

    public function setLastPlayDate(?\DateTimeInterface $lastPlayDate): self
    {
        $this->lastPlayDate = $lastPlayDate;

        return $this;
    }

    public function getFirstSkipDate(): ?\DateTimeInterface
    {
        return $this->firstSkipDate;
    }

    public function setFirstSkipDate(?\DateTimeInterface $firstSkipDate): self
    {
        $this->firstSkipDate = $firstSkipDate;

        return $this;
    }

    public function getLastSkipDate(): ?\DateTimeInterface
    {
        return $this->lastSkipDate;
    }

    public function setLastSkipDate(?\DateTimeInterface $lastSkipDate): self
    {
        $this->lastSkipDate = $lastSkipDate;

        return $this;
    }

    public function getBestRating(): ?int
    {
        return $this->bestRating;
    }

    public function setBestRating(?int $bestRating): self
    {
        $this->bestRating = $bestRating;

        return $this;
    }

    /**
     * @return Collection|SongTouch[]
     */
    public function getSongTouches(): Collection
    {
        return $this->songTouches;
    }

    public function addSongTouch(SongTouch $songTouch): self
    {
        if (!$this->songTouches->contains($songTouch)) {
            $this->songTouches[] = $songTouch;
            $songTouch->setSong($this);
        }

        return $this;
    }

    public function removeSongTouch(SongTouch $songTouch): self
    {
        if ($this->songTouches->contains($songTouch)) {
            $this->songTouches->removeElement($songTouch);
            // set the owning side to null (unless already changed)
            if ($songTouch->getSong() === $this) {
                $songTouch->setSong(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WorkHasSong[]
     */
    public function getWorkHasSongs(): Collection
    {
        return $this->workHasSongs;
    }

    public function addWorkHasSong(WorkHasSong $workHasSong): self
    {
        if (!$this->workHasSongs->contains($workHasSong)) {
            $this->workHasSongs[] = $workHasSong;
            $workHasSong->setSong($this);
        }

        return $this;
    }

    public function removeWorkHasSong(WorkHasSong $workHasSong): self
    {
        if ($this->workHasSongs->contains($workHasSong)) {
            $this->workHasSongs->removeElement($workHasSong);
            // set the owning side to null (unless already changed)
            if ($workHasSong->getSong() === $this) {
                $workHasSong->setSong(null);
            }
        }

        return $this;
    }

    public function getTitleNormalized(): ?string
    {
        return $this->titleNormalized;
    }

    public function setTitleNormalized(string $titleNormalized): self
    {
        $this->titleNormalized = $titleNormalized;

        return $this;
    }
}
