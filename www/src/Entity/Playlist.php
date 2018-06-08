<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlaylistRepository")
 */
class Playlist
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlaylistEntry", mappedBy="playlist", orphanRemoval=true)
     */
    private $playlistEntries;

    public function __construct()
    {
        $this->playlistEntries = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|PlaylistEntry[]
     */
    public function getPlaylistEntries(): Collection
    {
        return $this->playlistEntries;
    }

    public function addPlaylistEntry(PlaylistEntry $playlistEntry): self
    {
        if (!$this->playlistEntries->contains($playlistEntry)) {
            $this->playlistEntries[] = $playlistEntry;
            $playlistEntry->setPlaylist($this);
        }

        return $this;
    }

    public function removePlaylistEntry(PlaylistEntry $playlistEntry): self
    {
        if ($this->playlistEntries->contains($playlistEntry)) {
            $this->playlistEntries->removeElement($playlistEntry);
            // set the owning side to null (unless already changed)
            if ($playlistEntry->getPlaylist() === $this) {
                $playlistEntry->setPlaylist(null);
            }
        }

        return $this;
    }
}
