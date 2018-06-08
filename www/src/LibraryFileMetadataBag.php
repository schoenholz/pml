<?php

namespace App;

class LibraryFileMetadataBag
{
    private $values = [];

    public function getAll() : array
    {
        return $this->values;
    }

    public function setValues(string $attribute, array $values): self
    {
        $this->values[$attribute] = $values;

        return $this;
    }

    public function getValues(string $attribute): array
    {
        if (!$this->hasValues($attribute)) {
            return [];
        }

        return $this->values[$attribute];
    }

    public function hasValues(string $attribute): bool
    {
        return !array_key_exists($attribute, $this->values) || count($this->values[$attribute]) === 0;
    }
}
