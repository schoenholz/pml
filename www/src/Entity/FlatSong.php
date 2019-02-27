<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FlatSongRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="song_file_artist_title_unq", columns={"song_id", "file_id", "artist", "title"})
 * })
 */
class FlatSong
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
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $artist;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fileIsSynthetic;

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

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

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

    public function getFileIsSynthetic(): ?bool
    {
        return $this->fileIsSynthetic;
    }

    public function setFileIsSynthetic(bool $fileIsSynthetic): self
    {
        $this->fileIsSynthetic = $fileIsSynthetic;

        return $this;
    }
}
