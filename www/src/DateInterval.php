<?php

namespace App;

class DateInterval extends \DateInterval
{
    public function toSeconds(): int
    {
        return ($this->s)
            + ($this->i * 60)
            + ($this->h * 60 * 60)
            + ($this->d * 60 * 60 * 24)
            + ($this->m * 60 * 60 * 24 * 30)
            + ($this->y * 60 * 60 * 24 * 365);
    }
}
