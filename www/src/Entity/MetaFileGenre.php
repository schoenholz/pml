<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MetaFileGenreRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="meta_file_title_unq", columns={"meta_file_id", "title"})
 * })
 */
class MetaFileGenre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MetaFile", inversedBy="metaFileGenres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $metaFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    public function getId()
    {
        return $this->id;
    }

    public function getMetaFile(): ?MetaFile
    {
        return $this->metaFile;
    }

    public function setMetaFile(?MetaFile $metaFile): self
    {
        $this->metaFile = $metaFile;

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
