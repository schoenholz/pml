<?php

namespace App\FilterQuery\Token;

use App\FilterQuery\FilterQueryLexer;

class AttributeFilter extends AbstractToken
{
    /**
     * @var string
     */
    private $attribute;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var array
     */
    private $values;

    public function __construct(string $attribute, string $operator, array $values)
    {
        if (!in_array($operator, FilterQueryLexer::RELATIONAL_OPERATORS)) {
            throw new \InvalidArgumentException(sprintf('"%s" is no valid relational operator', $operator));
        }

        parent::__construct(FilterQueryLexer::TOKEN_ATTRIBUTE_FILTER);

        $this->attribute = $attribute;
        $this->operator = $operator;
        $this->values = $values;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
