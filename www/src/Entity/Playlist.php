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
     * @ORM\OneToMany(targetEntity="App\Entity\PlaylistItem", mappedBy="playlist", orphanRemoval=true)
     */
    private $playlistItems;

    public function __construct()
    {
        $this->playlistItems = new ArrayCollection();
    }

    public function getId(): ?int
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
     * @return Collection|PlaylistItem[]
     */
    public function getPlaylistItems(): Collection
    {
        return $this->playlistItems;
    }

    public function addPlaylistItem(PlaylistItem $playlistItem): self
    {
        if (!$this->playlistItems->contains($playlistItem)) {
            $this->playlistItems[] = $playlistItem;
            $playlistItem->setPlaylist($this);
        }

        return $this;
    }

    public function removePlaylistItem(PlaylistItem $playlistItem): self
    {
        if ($this->playlistItems->contains($playlistItem)) {
            $this->playlistItems->removeElement($playlistItem);
            // set the owning side to null (unless already changed)
            if ($playlistItem->getPlaylist() === $this) {
                $playlistItem->setPlaylist(null);
            }
        }

        return $this;
    }
}
