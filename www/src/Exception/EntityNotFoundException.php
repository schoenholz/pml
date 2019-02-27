<?php

namespace App\Exception;

use Throwable;

class EntityNotFoundException extends \Exception
{
    public function __construct(
        string $entityName,
        string $field = null,
        string $value = null,
        Throwable $previous = null
    ) {
        if ($field && $value) {
            parent::__construct(sprintf('%s with `%s` = `%s` not found', $entityName, $field, $value), 0, $previous);
        } else {
            parent::__construct(sprintf('%s not found', $entityName), 0, $previous);
        }
    }
}
