<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MetaFileRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="meta_lib_file_unq", columns={"meta_lib_id", "file_id"}),
 *     @ORM\UniqueConstraint(name="meta_lib_external_id_unq", columns={"meta_lib_id", "external_id"})
 * })
 */
class MetaFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MetaLib", inversedBy="metaFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $metaLib;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="metaFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titleNormalized;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $publisher;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="datetime", nullable=true)
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bpm;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $initialKey;

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
     * @ORM\OneToMany(targetEntity="App\Entity\MetaFileArtist", mappedBy="metaFile")
     */
    private $metaFileArtists;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MetaFileGenre", mappedBy="metaFile")
     */
    private $metaFileGenres;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MetaFileTouch", mappedBy="metaFile")
     */
    private $metaFileTouches;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MetaFileRating", mappedBy="metaFile")
     */
    private $metaFileRatings;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $musicBrainzId;

    public function __construct()
    {
        $this->metaFileArtists = new ArrayCollection();
        $this->metaFileGenres = new ArrayCollection();
        $this->metaFileTouches = new ArrayCollection();
        $this->metaFileRatings = new ArrayCollection();
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

    public function getTitleNormalized(): ?string
    {
        return $this->titleNormalized;
    }

    public function setTitleNormalized(string $titleNormalized): self
    {
        $this->titleNormalized = $titleNormalized;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

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

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;

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

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

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

    public function getAddedDate(): ?\DateTimeInterface
    {
        return $this->addedDate;
    }

    public function setAddedDate(\DateTimeInterface $addedDate): self
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    public function getFirstImportDate(): ?\DateTimeInterface
    {
        return $this->firstImportDate;
    }

    public function setFirstImportDate(\DateTimeInterface $firstImportDate): self
    {
        $this->firstImportDate = $firstImportDate;

        return $this;
    }

    public function getLastImportDate(): ?\DateTimeInterface
    {
        return $this->lastImportDate;
    }

    public function setLastImportDate(\DateTimeInterface $lastImportDate): self
    {
        $this->lastImportDate = $lastImportDate;

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

    public function getMetaLib(): ?MetaLib
    {
        return $this->metaLib;
    }

    public function setMetaLib(?MetaLib $metaLib): self
    {
        $this->metaLib = $metaLib;

        return $this;
    }

    /**
     * @return Collection|MetaFileArtist[]
     */
    public function getMetaFileArtists(): Collection
    {
        return $this->metaFileArtists;
    }

    public function addMetaFileArtist(MetaFileArtist $metaFileArtist): self
    {
        if (!$this->metaFileArtists->contains($metaFileArtist)) {
            $this->metaFileArtists[] = $metaFileArtist;
            $metaFileArtist->setMetaFile($this);
        }

        return $this;
    }

    public function removeMetaFileArtist(MetaFileArtist $metaFileArtist): self
    {
        if ($this->metaFileArtists->contains($metaFileArtist)) {
            $this->metaFileArtists->removeElement($metaFileArtist);
            // set the owning side to null (unless already changed)
            if ($metaFileArtist->getMetaFile() === $this) {
                $metaFileArtist->setMetaFile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MetaFileGenre[]
     */
    public function getMetaFileGenres(): Collection
    {
        return $this->metaFileGenres;
    }

    public function addMetaFileGenre(MetaFileGenre $metaFileGenre): self
    {
        if (!$this->metaFileGenres->contains($metaFileGenre)) {
            $this->metaFileGenres[] = $metaFileGenre;
            $metaFileGenre->setMetaFile($this);
        }

        return $this;
    }

    public function removeMetaFileGenre(MetaFileGenre $metaFileGenre): self
    {
        if ($this->metaFileGenres->contains($metaFileGenre)) {
            $this->metaFileGenres->removeElement($metaFileGenre);
            // set the owning side to null (unless already changed)
            if ($metaFileGenre->getMetaFile() === $this) {
                $metaFileGenre->setMetaFile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MetaFileTouch[]
     */
    public function getMetaFileTouches(): Collection
    {
        return $this->metaFileTouches;
    }

    public function addMetaFileTouch(MetaFileTouch $metaFileTouch): self
    {
        if (!$this->metaFileTouches->contains($metaFileTouch)) {
            $this->metaFileTouches[] = $metaFileTouch;
            $metaFileTouch->setMetaFile($this);
        }

        return $this;
    }

    public function removeMetaFileTouch(MetaFileTouch $metaFileTouch): self
    {
        if ($this->metaFileTouches->contains($metaFileTouch)) {
            $this->metaFileTouches->removeElement($metaFileTouch);
            // set the owning side to null (unless already changed)
            if ($metaFileTouch->getMetaFile() === $this) {
                $metaFileTouch->setMetaFile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MetaFileRating[]
     */
    public function getMetaFileRatings(): Collection
    {
        return $this->metaFileRatings;
    }

    public function addMetaFileRating(MetaFileRating $metaFileRating): self
    {
        if (!$this->metaFileRatings->contains($metaFileRating)) {
            $this->metaFileRatings[] = $metaFileRating;
            $metaFileRating->setMetaFile($this);
        }

        return $this;
    }

    public function removeMetaFileRating(MetaFileRating $metaFileRating): self
    {
        if ($this->metaFileRatings->contains($metaFileRating)) {
            $this->metaFileRatings->removeElement($metaFileRating);
            // set the owning side to null (unless already changed)
            if ($metaFileRating->getMetaFile() === $this) {
                $metaFileRating->setMetaFile(null);
            }
        }

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getMusicBrainzId(): ?string
    {
        return $this->musicBrainzId;
    }

    public function setMusicBrainzId(?string $musicBrainzId): self
    {
        $this->musicBrainzId = $musicBrainzId;

        return $this;
    }
}
