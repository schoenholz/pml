<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkHasSongRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="work_song_unq", columns={"work_id", "song_id"})
 * })
 */
class WorkHasSong
{
    const TYPE_PRIMARY = 'primary';
    const TYPE_DUPLICATE = 'duplicate';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Work", inversedBy="workHasSongs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $work;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Song", inversedBy="workHasSongs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $song;

    public function getId()
    {
        return $this->id;
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

    public function getWork(): ?Work
    {
        return $this->work;
    }

    public function setWork(?Work $work): self
    {
        $this->work = $work;

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
}
