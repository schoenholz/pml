<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkRepository")
 */
class Work
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
    private $rating;

    /**
     * @ORM\Column(type="datetime")
     */
    private $addedDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $daysInLibrary;

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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $daysBetweenFirstAndLastTouch;

    /**
     * @ORM\Column(type="integer")
     */
    private $daysSinceFirstTouch;

    /**
     * @ORM\Column(type="integer")
     */
    private $daysSinceLastTouch;

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
     * @ORM\Column(type="float")
     */
    private $playedPerTouchQuota;

    /**
     * @ORM\Column(type="float")
     */
    private $playedPerDayBetweenFirstAndLastTouchQuota;

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
     * @ORM\Column(type="float")
     */
    private $skippedPerTouchQuota;

    /**
     * @ORM\Column(type="float")
     */
    private $skippedPerDayBetweenFirstAndLastTouchQuota;

    /**
     * @ORM\Column(type="integer")
     */
    private $ratingScore;

    /**
     * @ORM\Column(type="integer")
     */
    private $bestRatingScore;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bestRatingScoreDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $lastTouchDateScore;

    /**
     * @ORM\Column(type="integer")
     */
    private $bestLastTouchDateScore;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bestLastTouchDateScoreDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $playCountScore;

    /**
     * @ORM\Column(type="integer")
     */
    private $bestPlayCountScore;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bestPlayCountScoreDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $playedPerTouchScore;

    /**
     * @ORM\Column(type="integer")
     */
    private $bestPlayedPerTouchScore;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bestPlayedPerTouchScoreDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WorkHasSong", mappedBy="work")
     */
    private $workHasSongs;

    public function __construct()
    {
        $this->workHasSongs = new ArrayCollection();
    }

    public function getOneWorkHasSongByType(string $type): WorkHasSong
    {
        foreach ($this->getWorkHasSongs() as $workHasSong) {
            if ($workHasSong->getType() == $type) {
                return $workHasSong;
            }
        }

        throw new \RuntimeException(sprintf(
            'No %s of type "%s" found for %s with ID %d',
            WorkHasSong::class,
            $type,
            Work::class,
            $this->getId()
        ));
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
            $workHasSong->setWork($this);
        }

        return $this;
    }

    public function removeWorkHasSong(WorkHasSong $workHasSong): self
    {
        if ($this->workHasSongs->contains($workHasSong)) {
            $this->workHasSongs->removeElement($workHasSong);
            // set the owning side to null (unless already changed)
            if ($workHasSong->getWork() === $this) {
                $workHasSong->setWork(null);
            }
        }

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
}
