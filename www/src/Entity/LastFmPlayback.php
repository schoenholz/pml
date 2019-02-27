<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LastFmPlaybackRepository")
 */
class LastFmPlayback
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $prec;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $artistTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $artistMbid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $trackTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $trackMbid;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getArtistTitle(): ?string
    {
        return $this->artistTitle;
    }

    public function setArtistTitle(string $artistTitle): self
    {
        $this->artistTitle = $artistTitle;

        return $this;
    }

    public function getArtistMbid(): ?string
    {
        return $this->artistMbid;
    }

    public function setArtistMbid(?string $artistMbid): self
    {
        $this->artistMbid = $artistMbid;

        return $this;
    }

    public function getTrackTitle(): ?string
    {
        return $this->trackTitle;
    }

    public function setTrackTitle(string $trackTitle): self
    {
        $this->trackTitle = $trackTitle;

        return $this;
    }

    public function getTrackMbid(): ?string
    {
        return $this->trackMbid;
    }

    public function setTrackMbid(?string $trackMbid): self
    {
        $this->trackMbid = $trackMbid;

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

    public function getPrec(): ?int
    {
        return $this->prec;
    }

    public function setPrec(int $prec): self
    {
        $this->prec = $prec;

        return $this;
    }
}
