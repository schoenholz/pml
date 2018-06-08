<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LibraryRepository")
 */
class Library
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
    private $path;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LibraryFile", mappedBy="library")
     */
    private $libraryFiles;

    public function __construct()
    {
        $this->libraryFiles = new ArrayCollection();
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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return Collection|LibraryFile[]
     */
    public function getLibraryFiles(): Collection
    {
        return $this->libraryFiles;
    }

    public function addLibraryFile(LibraryFile $libraryFile): self
    {
        if (!$this->libraryFiles->contains($libraryFile)) {
            $this->libraryFiles[] = $libraryFile;
            $libraryFile->setLibrary($this);
        }

        return $this;
    }

    public function removeLibraryFile(LibraryFile $libraryFile): self
    {
        if ($this->libraryFiles->contains($libraryFile)) {
            $this->libraryFiles->removeElement($libraryFile);
            // set the owning side to null (unless already changed)
            if ($libraryFile->getLibrary() === $this) {
                $libraryFile->setLibrary(null);
            }
        }

        return $this;
    }
}
