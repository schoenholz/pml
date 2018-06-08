<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LibraryFileAttributeValueRepository")
 */
class LibraryFileAttributeValue
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LibraryFile", inversedBy="libraryFileAttributeValues")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $libraryFile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LibraryFileAttribute")
     * @ORM\JoinColumn(nullable=false)
     */
    private $libraryFileAttribute;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $valueBool;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $valueDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $valueDateTime;

    /**
     * @ORM\Column(type="decimal", precision=30, scale=15, nullable=true)
     */
    private $valueFloat;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $valueInt;

    /**
     * @ORM\Column(type="string", length=4096, nullable=true)
     */
    private $valueString;

    public function getId()
    {
        return $this->id;
    }

    public function getLibraryFile(): ?LibraryFile
    {
        return $this->libraryFile;
    }

    public function setLibraryFile(?LibraryFile $libraryFile): self
    {
        $this->libraryFile = $libraryFile;

        return $this;
    }

    public function getValue()
    {
        if (!$this->getLibraryFileAttribute()) {
            throw new \RuntimeException('Unable to automatically get value if no attribute is set');
        }

        switch ($this->getLibraryFileAttribute()->getType()) {
            case LibraryFileAttribute::TYPE_BOOL:
                return $this->getValueBool();

            case LibraryFileAttribute::TYPE_DATE:
                return $this->getValueDate();

            case LibraryFileAttribute::TYPE_DATE_TIME:
                return $this->getValueDateTime();

            case LibraryFileAttribute::TYPE_FLOAT:
                return $this->getValueFloat();

            case LibraryFileAttribute::TYPE_INT:
                return $this->getValueInt();

            case LibraryFileAttribute::TYPE_STRING:
                return $this->getValueString();

            default:
                throw new \RuntimeException(sprintf('Attribute "%s" has no mapped type', $this->getLibraryFileAttribute()->getName()));
        }
    }

    public function setValue($value): self
    {
        if (!$this->getLibraryFileAttribute()) {
            throw new \RuntimeException('Unable to automatically set value if no attribute is set');
        }

        switch ($this->getLibraryFileAttribute()->getType()) {
            case LibraryFileAttribute::TYPE_BOOL:
                return $this->setValueBool($value);

            case LibraryFileAttribute::TYPE_DATE:
                return $this->setValueDate($value);

            case LibraryFileAttribute::TYPE_DATE_TIME:
                return $this->setValueDateTime($value);

            case LibraryFileAttribute::TYPE_FLOAT:
                return $this->setValueFloat($value);

            case LibraryFileAttribute::TYPE_INT:
                return $this->setValueInt($value);

            case LibraryFileAttribute::TYPE_STRING:
                return $this->setValueString($value);

            default:
                throw new \RuntimeException(sprintf('Attribute "%s" has no mapped type', $this->getLibraryFileAttribute()->getName()));
        }
    }

    public function getLibraryFileAttribute(): ?LibraryFileAttribute
    {
        return $this->libraryFileAttribute;
    }

    public function setLibraryFileAttribute(?LibraryFileAttribute $libraryFileAttribute): self
    {
        $this->libraryFileAttribute = $libraryFileAttribute;

        return $this;
    }

    public function getValueBool(): ?bool
    {
        return $this->valueBool;
    }

    public function setValueBool(?bool $valueBool): self
    {
        $this->valueBool = $valueBool;
        $this->valueDate = null;
        $this->valueDateTime = null;
        $this->valueFloat = null;
        $this->valueInt = null;
        $this->valueString = null;

        return $this;
    }

    public function getValueDate(): ?\DateTimeInterface
    {
        return $this->valueDate;
    }

    public function setValueDate(?\DateTimeInterface $valueDate): self
    {
        $this->valueBool = null;
        $this->valueDate = $valueDate;
        $this->valueDateTime = null;
        $this->valueFloat = null;
        $this->valueInt = null;
        $this->valueString = null;

        return $this;
    }

    public function getValueDateTime(): ?\DateTimeInterface
    {
        return $this->valueDateTime;
    }

    public function setValueDateTime(?\DateTimeInterface $valueDateTime): self
    {
        $this->valueBool = null;
        $this->valueDate = null;
        $this->valueDateTime = $valueDateTime;
        $this->valueFloat = null;
        $this->valueInt = null;
        $this->valueString = null;

        return $this;
    }

    public function getValueFloat(): ?float
    {
        return $this->valueFloat;
    }

    public function setValueFloat($valueFloat): self
    {
        $this->valueBool = null;
        $this->valueDate = null;
        $this->valueDateTime = null;
        $this->valueFloat = $valueFloat;
        $this->valueInt = null;
        $this->valueString = null;

        return $this;
    }

    public function getValueInt(): ?int
    {
        return $this->valueInt;
    }

    public function setValueInt(?int $valueInt): self
    {
        $this->valueBool = null;
        $this->valueDate = null;
        $this->valueDateTime = null;
        $this->valueFloat = null;
        $this->valueInt = $valueInt;
        $this->valueString = null;

        return $this;
    }

    public function getValueString(): ?string
    {
        return $this->valueString;
    }

    public function setValueString(?string $valueString): self
    {
        $this->valueBool = null;
        $this->valueDate = null;
        $this->valueDateTime = null;
        $this->valueFloat = null;
        $this->valueInt = null;
        $this->valueString = $valueString;

        return $this;
    }
}
