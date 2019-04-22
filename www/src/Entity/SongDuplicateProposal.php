<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SongDuplicateProposalRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="song_a_id_song_b_id_unq", columns={"song_a_id", "song_b_id"})
 * })
 */
class SongDuplicateProposal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDismissed;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Song")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $songA;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Song")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $songB;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $artist;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsDismissed(): ?bool
    {
        return $this->isDismissed;
    }

    public function setIsDismissed(bool $isDismissed): self
    {
        $this->isDismissed = $isDismissed;

        return $this;
    }

    public function getSongA(): ?Song
    {
        return $this->songA;
    }

    public function setSongA(?Song $songA): self
    {
        $this->songA = $songA;

        return $this;
    }

    public function getSongB(): ?Song
    {
        return $this->songB;
    }

    public function setSongB(?Song $songB): self
    {
        $this->songB = $songB;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): self
    {
        $this->artist = $artist;

        return $this;
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
}
