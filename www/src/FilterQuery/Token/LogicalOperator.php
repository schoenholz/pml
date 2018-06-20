<?php

namespace App\FilterQuery\Token;

use App\FilterQuery\FilterQueryLexer;

class LogicalOperator extends AbstractToken
{
    /**
     * @var string
     */
    private $operator;

    public function __construct(string $operator)
    {
        if (!in_array($operator, FilterQueryLexer::LOGICAL_OPERATORS)) {
            throw new \InvalidArgumentException(sprintf('"%s" is no valid logical operator', $operator));
        }

        parent::__construct(FilterQueryLexer::TOKEN_LOGICAL_OPERATOR);

        $this->operator = $operator;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }
}
