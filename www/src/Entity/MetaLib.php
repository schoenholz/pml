<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MetaLibRepository")
 */
class MetaLib
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
     * @ORM\Column(type="string", length=255)
     */
    private $rootPath;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MetaFile", mappedBy="metaLib")
     */
    private $metaFiles;

    public function __construct()
    {
        $this->metaFiles = new ArrayCollection();
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
            $metaFile->setMetaLib($this);
        }

        return $this;
    }

    public function removeMetaFile(MetaFile $metaFile): self
    {
        if ($this->metaFiles->contains($metaFile)) {
            $this->metaFiles->removeElement($metaFile);
            // set the owning side to null (unless already changed)
            if ($metaFile->getMetaLib() === $this) {
                $metaFile->setMetaLib(null);
            }
        }

        return $this;
    }

    public function getRootPath(): ?string
    {
        return $this->rootPath;
    }

    public function setRootPath(string $rootPath): self
    {
        $this->rootPath = $rootPath;

        return $this;
    }
}
