<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlaybackAggregationRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="song_id_period_unique", columns={"song_id", "period"})
 * })
 */
class PlaybackAggregation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Song")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $song;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $period;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalCount;

    /**
     * @ORM\Column(type="float")
     */
    private $percentage;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fromDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $toDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSong(): ?Song
    {
        return $this->song;
    }

    public function setSong(?Song $song): self
    {
        $this->song = $song;

        return $this;
    }

    public function getPeriod(): ?string
    {
        return $this->period;
    }

    public function setPeriod(string $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getTotalCount(): ?int
    {
        return $this->totalCount;
    }

    public function setTotalCount(int $totalCount): self
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(float $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(\DateTimeInterface $fromDate): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->toDate;
    }

    public function setToDate(\DateTimeInterface $toDate): self
    {
        $this->toDate = $toDate;

        return $this;
    }
}
