<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LibraryFileAttributeRepository")
 */
class LibraryFileAttribute
{
    const TYPE_BOOL = 'bool';
    const TYPE_DATE = 'date';
    const TYPE_DATE_TIME = 'date_time';
    const TYPE_FLOAT = 'float';
    const TYPE_INT = 'int';
    const TYPE_STRING = 'string';

    const TYPES = [
        self::TYPE_BOOL,
        self::TYPE_DATE,
        self::TYPE_DATE_TIME,
        self::TYPE_FLOAT,
        self::TYPE_INT,
        self::TYPE_STRING,
    ];

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
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isStatic;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, self::TYPES)) {
            throw new \InvalidArgumentException(sprintf('Invalid type "%s"; expected one of "$s"', $type, implode('", "', self::TYPES)));
        }

        $this->type = $type;

        return $this;
    }

    public function getIsStatic(): ?bool
    {
        return $this->isStatic;
    }

    public function setIsStatic(bool $isStatic): self
    {
        $this->isStatic = $isStatic;

        return $this;
    }

    public function getValueFieldName(): string
    {
        switch ($this->getType()) {
            case self::TYPE_BOOL:
                return 'valueBool';

            case self::TYPE_DATE:
                return 'valueDate';

            case self::TYPE_DATE_TIME:
                return 'valueDateTime';

            case self::TYPE_FLOAT:
                return 'valueFloat';

            case self::TYPE_INT:
                return 'valueInt';

            case self::TYPE_STRING:
                return 'valueString';

            default:
                throw new \RuntimeException(sprintf('Unknown attribute type "%s"', $this->getType()));
        }
    }
}
