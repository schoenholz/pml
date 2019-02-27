<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 */
class File
{
    const SONG_RELATION_DUPLICATE = 'duplicate';
    const SONG_RELATION_PRIMARY = 'primary';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Song", inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $song;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $songRelation;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $filePathName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MetaFile", mappedBy="file")
     */
    private $metaFiles;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSynthetic;

    public function __construct()
    {
        $this->metaFiles = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFilePathName(): ?string
    {
        return $this->filePathName;
    }

    public function setFilePathName(string $filePathName): self
    {
        $this->filePathName = $filePathName;

        return $this;
    }

    /**
     * @return Collection|MetaFile[]
     */
    public function getMetaFiles(): Collection
    {
        return $this->metaFiles;
    }

    public function addMetaFile(MetaFile $metaFile): self
    {
        if (!$this->metaFiles->contains($metaFile)) {
            $this->metaFiles[] = $metaFile;
            $metaFile->setFile($this);
        }

        return $this;
    }

    public function removeMetaFile(MetaFile $metaFile): self
    {
        if ($this->metaFiles->contains($metaFile)) {
            $this->metaFiles->removeElement($metaFile);
            // set the owning side to null (unless already changed)
            if ($metaFile->getFile() === $this) {
                $metaFile->setFile(null);
            }
        }

        return $this;
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

    public function getSongRelation(): ?string
    {
        return $this->songRelation;
    }

    public function setSongRelation(string $songRelation): self
    {
        $this->songRelation = $songRelation;

        return $this;
    }

    public function getIsSynthetic(): ?bool
    {
        return $this->isSynthetic;
    }

    public function setIsSynthetic(bool $isSynthetic): self
    {
        $this->isSynthetic = $isSynthetic;

        return $this;
    }
}
