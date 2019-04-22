<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MetaFileTouchRepository")
 */
class MetaFileTouch
{
    const TYPE_PLAY = 'play';
    const TYPE_SKIP = 'skip';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MetaFile", inversedBy="metaFileTouches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $metaFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $prec;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date = null): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getPrec(): ?int
    {
        return $this->prec;
    }

    public function setPrec(int $prec): self
    {
        $this->prec = $prec;

        return $this;
    }
}
