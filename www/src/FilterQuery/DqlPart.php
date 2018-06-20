<?php

namespace App\FilterQuery;

class DqlPart
{
    /**
     * @var string
     */
    private $dql;

    /**
     * @var array
     */
    private $params;

    public function __construct(string $dql, array $params = [])
    {
        $this->dql = $dql;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getDql(): string
    {
        return $this->dql;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
