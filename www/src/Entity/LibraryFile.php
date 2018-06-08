<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LibraryFileRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="path_name_unq", columns={"path", "name"})
 * })
 */
class LibraryFile
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
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFileExists;

    /**
     * todo Make attribute
     * @ORM\Column(type="datetime")
     */
    private $fileCreatedAt;

    /**
     * todo Make attribute
     * @ORM\Column(type="datetime")
     */
    private $fileModifiedAt;

    /**
     * todo Make attribute
     * @ORM\Column(type="integer")
     */
    private $fileSize;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $analyzedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $scannedAt;

    /**
     * todo Clone as attribute
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Library", inversedBy="libraryFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $library;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LibraryFileAttributeValue", mappedBy="libraryFile")
     */
    private $libraryFileAttributeValues;

    public function __construct()
    {
        $this->libraryFileAttributeValues = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLibraryPathName(): ?string
    {
        if (!$this->getLibrary()) {
            throw new \RuntimeException('No Library is set');
        }

        if (!$this->getName()) {
            return null;
        }

        $parts = [
            rtrim($this->getLibrary()->getPath(), '/'),
            trim($this->getPath(), '/'),
            $this->getName(),
        ];

        return implode(array_filter($parts), '/');
    }

    public function getPathName(): ?string
    {
        $path = rtrim((string) $this->getPath(), '/');

        if (!empty($path)) {
            return $path . '/' . $this->getName();
        }

        return $this->getName();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsFileExists(): ?bool
    {
        return $this->isFileExists;
    }

    public function setIsFileExists(bool $isFileExists): self
    {
        $this->isFileExists = $isFileExists;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updatedAt = $updated_at;

        return $this;
    }

    public function getLibrary(): ?Library
    {
        return $this->library;
    }

    public function setLibrary(?Library $library): self
    {
        $this->library = $library;

        return $this;
    }

    public function getAnalyzedAt(): ?\DateTimeInterface
    {
        return $this->analyzedAt;
    }

    public function setAnalyzedAt(?\DateTimeInterface $analyzedAt): self
    {
        $this->analyzedAt = $analyzedAt;

        return $this;
    }

    public function getScannedAt(): ?\DateTimeInterface
    {
        return $this->scannedAt;
    }

    public function setScannedAt(?\DateTimeInterface $scannedAt): self
    {
        $this->scannedAt = $scannedAt;

        return $this;
    }

    /**
     * @return Collection|LibraryFileAttributeValue[]
     */
    public function getLibraryFileAttributeValues(): Collection
    {
        return $this->libraryFileAttributeValues;
    }

    public function addLibraryFileAttributeValue(LibraryFileAttributeValue $libraryFileAttributeValue): self
    {
        if (!$this->libraryFileAttributeValues->contains($libraryFileAttributeValue)) {
            $this->libraryFileAttributeValues[] = $libraryFileAttributeValue;
            $libraryFileAttributeValue->setLibraryFile($this);
        }

        return $this;
    }

    public function removeLibraryFileAttributeValue(LibraryFileAttributeValue $libraryFileAttributeValue): self
    {
        if ($this->libraryFileAttributeValues->contains($libraryFileAttributeValue)) {
            $this->libraryFileAttributeValues->removeElement($libraryFileAttributeValue);
            // set the owning side to null (unless already changed)
            if ($libraryFileAttributeValue->getLibraryFile() === $this) {
                $libraryFileAttributeValue->setLibraryFile(null);
            }
        }

        return $this;
    }

    public function getFileCreatedAt(): ?\DateTimeInterface
    {
        return $this->fileCreatedAt;
    }

    public function setFileCreatedAt(\DateTimeInterface $fileCreatedAt): self
    {
        $this->fileCreatedAt = $fileCreatedAt;

        return $this;
    }

    public function getFileModifiedAt(): ?\DateTimeInterface
    {
        return $this->fileModifiedAt;
    }

    public function setFileModifiedAt(\DateTimeInterface $fileModifiedAt): self
    {
        $this->fileModifiedAt = $fileModifiedAt;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }
}
