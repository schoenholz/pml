<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MetaFileArtistRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="meta_file_relation_title_unq", columns={"meta_file_id", "relation", "title"})
 * })
 */
class MetaFileArtist
{
    const RELATION_ARTISTS = 'artist';
    const RELATION_INVOLVED = 'involved';
    const RELATION_PERFORMER = 'performer';
    const RELATION_WRITER = 'writer';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MetaFile", inversedBy="metaFileArtists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $metaFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $relation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titleNormalized;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $musicBrainzId;

    public function getId()
    {
        return $this->id;
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

    public function getMetaFile(): ?MetaFile
    {
        return $this->metaFile;
    }

    public function setMetaFile(?MetaFile $metaFile): self
    {
        $this->metaFile = $metaFile;

        return $this;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function setRelation(string $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    public function getTitleNormalized(): ?string
    {
        return $this->titleNormalized;
    }

    public function setTitleNormalized(string $titleNormalized): self
    {
        $this->titleNormalized = $titleNormalized;

        return $this;
    }

    public function getMusicBrainzId(): ?string
    {
        return $this->musicBrainzId;
    }

    public function setMusicBrainzId(?string $musicBrainzId): self
    {
        $this->musicBrainzId = $musicBrainzId;

        return $this;
    }
}
