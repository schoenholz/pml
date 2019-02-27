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
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $addedDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $daysInLibrary = 1;

    /**
     * @ORM\Column(type="integer")
     */
    private $touchCount = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstTouchDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastTouchDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $daysBetweenFirstAndLastTouch;

    /**
     * @ORM\Column(type="integer")
     */
    private $daysSinceFirstTouch = 1;

    /**
     * @ORM\Column(type="integer")
     */
    private $daysSinceLastTouch = 1;

    /**
     * @ORM\Column(type="integer")
     */
    private $playCount = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstPlayDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastPlayDate;

    /**
     * @ORM\Column(type="float")
     */
    private $playedPerTouchQuota = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $playedPerDayBetweenFirstAndLastTouchQuota = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $skipCount = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstSkipDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastSkipDate;

    /**
     * @ORM\Column(type="float")
     */
    private $skippedPerTouchQuota = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $skippedPerDayBetweenFirstAndLastTouchQuota = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $ratingScore = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $bestRatingScore = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bestRatingScoreDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $lastTouchDateScore = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $bestLastTouchDateScore = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bestLastTouchDateScoreDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $playCountScore = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $bestPlayCountScore = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bestPlayCountScoreDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $playedPerTouchScore = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $bestPlayedPerTouchScore = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bestPlayedPerTouchScoreDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\File", mappedBy="song")
     */
    private $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getPrimaryFile(): File
    {
        $primaries = $this
            ->getFiles()
            ->filter(function (File $file): bool {
                return $file->getSongRelation() == File::SONG_RELATION_PRIMARY;
            })
        ;

        if ($primaries->isEmpty()) {
            throw new \RuntimeException(sprintf('Song %d has no primary file', $this->getId()));
        }

        if ($primaries->count() > 1) {
            throw new \RuntimeException(sprintf('Song %d has multiple primary files', $this->getId()));
        }

        return $primaries->first();
    }

    public function getId()
    {
        return $this->id;
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

    public function getDaysBetweenFirstAndLastTouch(): ?int
    {
        return $this->daysBetweenFirstAndLastTouch;
    }

    public function setDaysBetweenFirstAndLastTouch(?int $daysBetweenFirstAndLastTouch): self
    {
        $this->daysBetweenFirstAndLastTouch = $daysBetweenFirstAndLastTouch;

        return $this;
    }

    public function getDaysSinceFirstTouch(): ?int
    {
        return $this->daysSinceFirstTouch;
    }

    public function setDaysSinceFirstTouch(int $daysSinceFirstTouch): self
    {
        $this->daysSinceFirstTouch = $daysSinceFirstTouch;

        return $this;
    }

    public function getDaysSinceLastTouch(): ?int
    {
        return $this->daysSinceLastTouch;
    }

    public function setDaysSinceLastTouch(int $daysSinceLastTouch): self
    {
        $this->daysSinceLastTouch = $daysSinceLastTouch;

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

    public function getPlayedPerTouchQuota(): ?float
    {
        return $this->playedPerTouchQuota;
    }

    public function setPlayedPerTouchQuota(float $playedPerTouchQuota): self
    {
        $this->playedPerTouchQuota = $playedPerTouchQuota;

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

    public function getSkippedPerTouchQuota(): ?float
    {
        return $this->skippedPerTouchQuota;
    }

    public function setSkippedPerTouchQuota(float $skippedPerTouchQuota): self
    {
        $this->skippedPerTouchQuota = $skippedPerTouchQuota;

        return $this;
    }

    public function getPlayedPerDayBetweenFirstAndLastTouchQuota(): ?float
    {
        return $this->playedPerDayBetweenFirstAndLastTouchQuota;
    }

    public function setPlayedPerDayBetweenFirstAndLastTouchQuota(float $playedPerDayBetweenFirstAndLastTouchQuota): self
    {
        $this->playedPerDayBetweenFirstAndLastTouchQuota = $playedPerDayBetweenFirstAndLastTouchQuota;

        return $this;
    }

    public function getSkippedPerDayBetweenFirstAndLastTouchQuota(): ?float
    {
        return $this->skippedPerDayBetweenFirstAndLastTouchQuota;
    }

    public function setSkippedPerDayBetweenFirstAndLastTouchQuota(float $skippedPerDayBetweenFirstAndLastTouchQuota): self
    {
        $this->skippedPerDayBetweenFirstAndLastTouchQuota = $skippedPerDayBetweenFirstAndLastTouchQuota;

        return $this;
    }

    public function getDaysInLibrary(): ?int
    {
        return $this->daysInLibrary;
    }

    public function setDaysInLibrary(int $daysInLibrary): self
    {
        $this->daysInLibrary = $daysInLibrary;

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

    public function getRatingScore(): ?int
    {
        return $this->ratingScore;
    }

    public function setRatingScore(int $ratingScore): self
    {
        $this->ratingScore = $ratingScore;

        return $this;
    }

    public function getLastTouchDateScore(): ?int
    {
        return $this->lastTouchDateScore;
    }

    public function setLastTouchDateScore(int $lastTouchDateScore): self
    {
        $this->lastTouchDateScore = $lastTouchDateScore;

        return $this;
    }

    public function getPlayCountScore(): ?int
    {
        return $this->playCountScore;
    }

    public function setPlayCountScore(int $playCountScore): self
    {
        $this->playCountScore = $playCountScore;

        return $this;
    }

    public function getPlayedPerTouchScore(): ?int
    {
        return $this->playedPerTouchScore;
    }

    public function setPlayedPerTouchScore(int $playedPerTouchScore): self
    {
        $this->playedPerTouchScore = $playedPerTouchScore;

        return $this;
    }

    public function getBestRatingScore(): ?int
    {
        return $this->bestRatingScore;
    }

    public function setBestRatingScore(int $bestRatingScore): self
    {
        $this->bestRatingScore = $bestRatingScore;

        return $this;
    }

    public function getBestRatingScoreDate(): ?\DateTimeInterface
    {
        return $this->bestRatingScoreDate;
    }

    public function setBestRatingScoreDate(\DateTimeInterface $bestRatingScoreDate): self
    {
        $this->bestRatingScoreDate = $bestRatingScoreDate;

        return $this;
    }

    public function getBestLastTouchDateScore(): ?int
    {
        return $this->bestLastTouchDateScore;
    }

    public function setBestLastTouchDateScore(int $bestLastTouchDateScore): self
    {
        $this->bestLastTouchDateScore = $bestLastTouchDateScore;

        return $this;
    }

    public function getBestLastTouchDateScoreDate(): ?\DateTimeInterface
    {
        return $this->bestLastTouchDateScoreDate;
    }

    public function setBestLastTouchDateScoreDate(\DateTimeInterface $bestLastTouchDateScoreDate): self
    {
        $this->bestLastTouchDateScoreDate = $bestLastTouchDateScoreDate;

        return $this;
    }

    public function getBestPlayCountScore(): ?int
    {
        return $this->bestPlayCountScore;
    }

    public function setBestPlayCountScore(int $bestPlayCountScore): self
    {
        $this->bestPlayCountScore = $bestPlayCountScore;

        return $this;
    }

    public function getBestPlayCountScoreDate(): ?\DateTimeInterface
    {
        return $this->bestPlayCountScoreDate;
    }

    public function setBestPlayCountScoreDate(\DateTimeInterface $bestPlayCountScoreDate): self
    {
        $this->bestPlayCountScoreDate = $bestPlayCountScoreDate;

        return $this;
    }

    public function getBestPlayedPerTouchScore(): ?int
    {
        return $this->bestPlayedPerTouchScore;
    }

    public function setBestPlayedPerTouchScore(int $bestPlayedPerTouchScore): self
    {
        $this->bestPlayedPerTouchScore = $bestPlayedPerTouchScore;

        return $this;
    }

    public function getBestPlayedPerTouchScoreDate(): ?\DateTimeInterface
    {
        return $this->bestPlayedPerTouchScoreDate;
    }

    public function setBestPlayedPerTouchScoreDate(\DateTimeInterface $bestPlayedPerTouchScoreDate): self
    {
        $this->bestPlayedPerTouchScoreDate = $bestPlayedPerTouchScoreDate;

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

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setSong($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getSong() === $this) {
                $file->setSong(null);
            }
        }

        return $this;
    }
}
