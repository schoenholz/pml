<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlaylistEntryRepository")
 */
class PlaylistEntry
{
    const STATE_ACTIVE = 1;
    const STATE_AUTO = 0;
    const STATE_INACTIVE = -1;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Playlist", inversedBy="playlistEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $playlist;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LibraryFile")
     * @ORM\JoinColumn(nullable=false)
     */
    private $libraryFile;

    public function getId()
    {
        return $this->id;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPlaylist(): ?Playlist
    {
        return $this->playlist;
    }

    public function setPlaylist(?Playlist $playlist): self
    {
        $this->playlist = $playlist;

        return $this;
    }

    public function getLibraryFile(): ?LibraryFile
    {
        return $this->libraryFile;
    }

    public function setLibraryFile(?LibraryFile $libraryFile): self
    {
        $this->libraryFile = $libraryFile;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
