<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaMonkeySongRepository")
 */
class MediaMonkeySong
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     */
    private $mediaMonkeyId;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $filePathName;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $artist;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $publisher;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $album;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $discNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $trackNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $playCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $skipCount;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastPlayedDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstPlayedDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bpm;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $initialKey;

    /**
     * @ORM\Column(type="datetime")
     */
    private $addedDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bitrate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $samplingFrequency;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $genre;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletionDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    public function getId()
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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

    public function setFilePathName(string $filePathName): self
    {
        $this->filePathName = $filePathName;

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

    public function getLastPlayedDate(): ?\DateTimeInterface
    {
        return $this->lastPlayedDate;
    }

    public function setLastPlayedDate(?\DateTimeInterface $lastPlayedDate): self
    {
        $this->lastPlayedDate = $lastPlayedDate;

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

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;

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

    public function getBitrate(): ?int
    {
        return $this->bitrate;
    }

    public function setBitrate(?int $bitrate): self
    {
        $this->bitrate = $bitrate;

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

    public function getFirstPlayedDate(): ?\DateTimeInterface
    {
        return $this->firstPlayedDate;
    }

    public function setFirstPlayedDate(?\DateTimeInterface $firstPlayedDate): self
    {
        $this->firstPlayedDate = $firstPlayedDate;

        return $this;
    }

    public function getArtist(): array
    {
        return $this->artist
            ? json_decode($this->artist, true)
            : [];
    }

    public function setArtist(array $artist): self
    {
        $this->artist = json_encode(array_values($artist));

        return $this;
    }

    public function getGenre(): array
    {
        return $this->genre
            ? json_decode($this->genre, true)
            : [];
    }

    public function setGenre(array $genre): self
    {
        $this->genre = json_encode(array_values($genre));

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

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
