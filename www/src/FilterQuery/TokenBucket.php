<?php

namespace App\FilterQuery;

use App\FilterQuery\Token\AbstractToken;

class TokenBucket implements \Countable, \Iterator
{
    private $position = 0;

    /**
     * @var AbstractToken[]
     */
    private $tokens = [];

    public function addToken(AbstractToken $fragment): self
    {
        $this->tokens[] = $fragment;

        return $this;
    }

    public function count()
    {
        return count($this->tokens);
    }

    public function current()
    {
        return $this->tokens[$this->position];
    }

    public function next()
    {
        ++ $this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return array_key_exists($this->position, $this->tokens);
    }

    public function rewind()
    {
        $this->position = 0;
    }
}
