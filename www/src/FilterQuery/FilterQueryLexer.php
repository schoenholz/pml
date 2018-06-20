<?php

namespace App\FilterQuery;

use App\Entity\LibraryFileAttribute;
use App\Exception\FilterQuerySemanticException;
use App\Exception\FilterQuerySyntaxException;
use App\FilterQuery\Token\AbstractToken;
use Doctrine\ORM\EntityManagerInterface;

class FilterQueryLexer
{
    const TOKEN_ATTRIBUTE_NAME = 'T_ATTRIBUTE_NAME';
    const TOKEN_LOGICAL_OPERATOR = 'T_LOGICAL_OPERATOR';
    const TOKEN_RELATIONAL_OPERATOR = 'T_RELATIONAL_OPERATOR';
    const TOKEN_VALUE = 'T_VALUE';
    const TOKEN_PARENTHESIS_OPENING = 'T_PARENTHESIS_OPENING';
    const TOKEN_PARENTHESIS_CLOSING = 'T_PARENTHESIS_CLOSING';

    const TOKEN_ATTRIBUTE_FILTER = 'T_ATTRIBUTE_FILTER';

    const RELATIONAL_OPERATOR_EQUAL = 'RELATIONAL_OPERATOR_EQUAL';
    const RELATIONAL_OPERATOR_NOT_EQUAL = 'RELATIONAL_OPERATOR_NOT_EQUAL';
    const RELATIONAL_OPERATOR_CONTAINS = 'RELATIONAL_OPERATOR_CONTAINS';
    const RELATIONAL_OPERATOR_NOT_CONTAINS = 'RELATIONAL_OPERATOR_NOT_CONTAINS';
    const RELATIONAL_OPERATOR_GREATER = 'RELATIONAL_OPERATOR_GREATER';
    const RELATIONAL_OPERATOR_GREATER_EQUAL = 'RELATIONAL_OPERATOR_GREATER_EQUAL';
    const RELATIONAL_OPERATOR_LESS = 'RELATIONAL_OPERATOR_LESS';
    const RELATIONAL_OPERATOR_LESS_EQUAL = 'RELATIONAL_OPERATOR_LESS_EQUAL';

    const RELATIONAL_OPERATORS = [
        self::RELATIONAL_OPERATOR_EQUAL,
        self::RELATIONAL_OPERATOR_NOT_EQUAL,
        self::RELATIONAL_OPERATOR_CONTAINS,
        self::RELATIONAL_OPERATOR_NOT_CONTAINS,
        self::RELATIONAL_OPERATOR_GREATER,
        self::RELATIONAL_OPERATOR_GREATER_EQUAL,
        self::RELATIONAL_OPERATOR_LESS,
        self::RELATIONAL_OPERATOR_LESS_EQUAL,
    ];

    const SUPPORTED_RELATIONAL_OPERATORS = [
        LibraryFileAttribute::TYPE_BOOL => [
            self::RELATIONAL_OPERATOR_EQUAL,
            self::RELATIONAL_OPERATOR_NOT_EQUAL,
        ],
        LibraryFileAttribute::TYPE_DATE => [
            self::RELATIONAL_OPERATOR_EQUAL,
            self::RELATIONAL_OPERATOR_NOT_EQUAL,
            self::RELATIONAL_OPERATOR_GREATER,
            self::RELATIONAL_OPERATOR_GREATER_EQUAL,
            self::RELATIONAL_OPERATOR_LESS,
            self::RELATIONAL_OPERATOR_LESS_EQUAL,
        ],
        LibraryFileAttribute::TYPE_DATE_TIME => [
            self::RELATIONAL_OPERATOR_EQUAL,
            self::RELATIONAL_OPERATOR_NOT_EQUAL,
            self::RELATIONAL_OPERATOR_GREATER,
            self::RELATIONAL_OPERATOR_GREATER_EQUAL,
            self::RELATIONAL_OPERATOR_LESS,
            self::RELATIONAL_OPERATOR_LESS_EQUAL,
        ],
        LibraryFileAttribute::TYPE_FLOAT => [
            self::RELATIONAL_OPERATOR_EQUAL,
            self::RELATIONAL_OPERATOR_NOT_EQUAL,
            self::RELATIONAL_OPERATOR_GREATER,
            self::RELATIONAL_OPERATOR_GREATER_EQUAL,
            self::RELATIONAL_OPERATOR_LESS,
            self::RELATIONAL_OPERATOR_LESS_EQUAL,
        ],
        LibraryFileAttribute::TYPE_INT => [
            self::RELATIONAL_OPERATOR_EQUAL,
            self::RELATIONAL_OPERATOR_NOT_EQUAL,
            self::RELATIONAL_OPERATOR_GREATER,
            self::RELATIONAL_OPERATOR_GREATER_EQUAL,
            self::RELATIONAL_OPERATOR_LESS,
            self::RELATIONAL_OPERATOR_LESS_EQUAL,
        ],
        LibraryFileAttribute::TYPE_STRING => [
            self::RELATIONAL_OPERATOR_EQUAL,
            self::RELATIONAL_OPERATOR_NOT_EQUAL,
            self::RELATIONAL_OPERATOR_CONTAINS,
            self::RELATIONAL_OPERATOR_NOT_CONTAINS,
        ],
    ];

    const LOGICAL_OPERATOR_AND = 'LOGICAL_OPERATOR_AND';
    const LOGICAL_OPERATOR_OR = 'LOGICAL_OPERATOR_OR';

    const LOGICAL_OPERATORS = [
        self::LOGICAL_OPERATOR_AND,
        self::LOGICAL_OPERATOR_OR,
    ];

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValueCaster
     */
    private $valueCaster;

    /**
     * @var LibraryFileAttribute[]
     */
    private $libraryFileAttributes;

    /**
     * @var TokenBucket
     */
    private $bucket;

    /**
     * Current position.
     *
     * @var int
     */
    private $position;

    /**
     * All chars of the given query.
     *
     * @var array
     */
    private $chunks;

    /**
     * Positions of opening parenthesis.
     *
     * @var array
     */
    private $openedParenthesis;

    /**
     * The next expected token type.
     *
     * @var string
     */
    private $expect;

    /**
     * @var string
     */
    private $tmpAttributeName;

    /**
     * @var array
     */
    private $tmpValues;

    /**
     * @var string|null
     */
    private $tmpOperator;

    /**
     * @var int
     */
    private $valueIndex;

    public function __construct(EntityManagerInterface $entityManager, ValueCaster $valueCaster)
    {
        $this->valueCaster = $valueCaster;
        $this->entityManager = $entityManager;
    }

    public function parse(string $qry): TokenBucket
    {
        $this->load($qry);

        while (!$this->done()) {
            // Skip all not quoted whitespaces.
            $this->skipWhitespace();

            // Consumed all whitespaces at the end of $qry.
            if ($this->done()) {
                break;
            }

            if ($this->expects(self::TOKEN_LOGICAL_OPERATOR)) {
                $this->parseLogicalOperatorOrClosingParenthesis();
            }

            elseif ($this->expects(self::TOKEN_ATTRIBUTE_NAME)) {
                $this->parseAttributeNameOrOpeningParenthesis();
            }

            elseif ($this->expects(self::TOKEN_RELATIONAL_OPERATOR)) {
                $this->parseRelationalOperator();
            }

            elseif ($this->expects(self::TOKEN_VALUE)) {
                $this->parseValue();
            }

            else {
                // WTF happened?!
                throw new \RuntimeException(sprintf('Did not expect that "%s" was expected at position %d', $this->expect, $this->position()));
            }
        }

        if ($this->isInParenthesis()) {
            throw new FilterQuerySyntaxException(sprintf('Opening parenthesis at position %d was not closed', array_pop($this->openedParenthesis)));
        }

        return $this->bucket;
    }

    protected function getAttribute(string $attributeName): LibraryFileAttribute
    {
        $this->loadLibraryFileAttributes();

        if (!array_key_exists($attributeName, $this->libraryFileAttributes)) {
            throw new FilterQuerySemanticException(sprintf('Unknown attribute "%s"', $attributeName));
        }

        return $this->libraryFileAttributes[$attributeName];
    }

    protected function loadLibraryFileAttributes()
    {
        if ($this->libraryFileAttributes === null) {
            $this->libraryFileAttributes= [];

            /** @var LibraryFileAttribute $a */
            foreach ($this->entityManager->getRepository(LibraryFileAttribute::class)->findAll() as $a) {
                $this->libraryFileAttributes[$a->getName()] = $a;
            }
        }
    }

    private function parseLogicalOperatorOrClosingParenthesis()
    {
        if ($this->is(')')) {
            if (!$this->isInParenthesis()) {
                throw new FilterQuerySyntaxException(sprintf('Closing parenthesis at position %d was not opened', $this->position()));
            }

            $this->token(new Token\ClosingParenthesis());
            $this->move();
            array_pop($this->openedParenthesis);
        } elseif ($this->is('&')) {
            $this->token(new Token\LogicalOperator(self::LOGICAL_OPERATOR_AND));
            $this->expect(self::TOKEN_ATTRIBUTE_NAME);
            $this->move();
        } elseif ($this->is('|')) {
            $this->token(new Token\LogicalOperator(self::LOGICAL_OPERATOR_OR));
            $this->expect(self::TOKEN_ATTRIBUTE_NAME);
            $this->move();
        } else {
            throw new FilterQuerySyntaxException(sprintf('Unexpected "%s" as position %d; expected logical operator', $this->current(), $this->position()));
        }
    }

    private function parseAttributeNameOrOpeningParenthesis()
    {
        if ($this->is('(')) {
            $this->token(new Token\OpeningParenthesis());
            $this->openedParenthesis[] = $this->position();
            $this->move();

            return;
        } else {
            if (!preg_match('/^[a-z0-9_]$/i', $this->current())) {
                throw new FilterQuerySyntaxException(sprintf('Expected attribute at position %d; got "%s"', $this->position(), $this->current()));
            }

            // Consume all attribute chars.
            while (preg_match('/^[a-z0-9_]$/i', $this->current())) {
                $this->tmpAttributeName .= $this->current();
                $this->move();
            }

            $this->expect(self::TOKEN_RELATIONAL_OPERATOR);
        }
    }

    private function parseRelationalOperator()
    {
        if ($this->is('!')) {
            if ($this->will('=')) {
                $this->tmpOperator = self::RELATIONAL_OPERATOR_NOT_EQUAL;
                $this->move();
            } elseif ($this->will('~')) {
                $this->tmpOperator = self::RELATIONAL_OPERATOR_NOT_CONTAINS;
                $this->move();
            } else {
                throw new FilterQuerySyntaxException(sprintf('Unexpected "!" at position %d', $this->position()));
            }
        } elseif ($this->is('=')) {
            $this->tmpOperator = self::RELATIONAL_OPERATOR_EQUAL;
        } elseif ($this->is('~')) {
            $this->tmpOperator = self::RELATIONAL_OPERATOR_CONTAINS;
        } elseif ($this->is('<')) {
            if ($this->will('=')) {
                $this->tmpOperator = self::RELATIONAL_OPERATOR_LESS_EQUAL;
                $this->move();
            } else {
                $this->tmpOperator = self::RELATIONAL_OPERATOR_LESS;
            }
        } elseif ($this->is('>')) {
            if ($this->will('=')) {
                $this->tmpOperator = self::RELATIONAL_OPERATOR_GREATER_EQUAL;
                $this->move();
            } else {
                $this->tmpOperator = self::RELATIONAL_OPERATOR_GREATER;
            }
        } else {
            throw new FilterQuerySyntaxException(sprintf('Unexpected "%s" at position %d; expected relational operator', $this->current(), $this->position()));
        }

        $this->assertLegalRelationalOperator();
        $this->move();
        $this->expect(self::TOKEN_VALUE);
    }

    private function parseValue()
    {
        if ($this->is('"') || $this->is('\'')) {
            $quote = $this->current();
            $quotePos = $this->position();
            // Consume all chars until the quote gets closed.
            while (true) {
                $this->move();

                if ($this->done()) {
                    // Reached the end of $qry before the quote is closed.
                    throw new FilterQuerySyntaxException(sprintf('Quote starting at position %d is not closed', $quotePos));
                }

                if ($this->current() === $quote && !$this->was('\\')) {
                    // Found the end of the quote. Stop consuming chars.
                    break;
                }

                if (!isset($this->tmpValues[$this->valueIndex])) {
                    $this->tmpValues[$this->valueIndex] = '';
                }

                if ($this->is('\\') && $this->will($quote)) {
                    // Skip escape char.
                    continue;
                }

                $this->tmpValues[$this->valueIndex] .= $this->current();
            }

            $this->move();
        } elseif (!preg_match('/^\w$/', $this->current())) {
            throw new FilterQuerySyntaxException(sprintf('Value must be quoted if starting with "%s" at position %d', $this->current(), $this->position()));
        } else {
            // Consume all chars of a non-quoted value.
            while (
                !$this->done()
                && preg_match('/^[\w.]$/', $this->current())
            ) {
                if (!isset($this->tmpValues[$this->valueIndex])) {
                    $this->tmpValues[$this->valueIndex] = '';
                }

                $this->tmpValues[$this->valueIndex] .= $this->current();
                $this->move();
            }
        }

        // Ignore whitespace after values to be able to peek next char.
        $this->skipWhitespace();

        $attribute = $this->getAttribute($this->tmpAttributeName);

        // Check if an enumeration is indicated.
        if (
            $this->done()
            || !$this->is(',')
        ) {
            $this->token(new Token\AttributeFilter(
                $this->tmpAttributeName,
                $this->tmpOperator,
                $this->valueCaster->castMulti($attribute, $this->tmpValues)
            ));
            $this->valueIndex = 0;
            $this->tmpAttributeName = '';
            $this->tmpValues = [];
            $this->tmpOperator = null;
            $this->expect(self::TOKEN_LOGICAL_OPERATOR);
        } else {
            if (!in_array($this->tmpOperator, [
                self::RELATIONAL_OPERATOR_EQUAL,
                self::RELATIONAL_OPERATOR_NOT_EQUAL,
                self::RELATIONAL_OPERATOR_CONTAINS,
                self::RELATIONAL_OPERATOR_NOT_CONTAINS,
            ])) {
                // The defined relational operator cannot not be used with multiple values.
                throw new FilterQuerySemanticException(sprintf('Unexpected comma at position %d; relational operator "%s" cannot be used with multiple values', $this->position(), $this->tmpOperator));
            }

            if ($attribute->getType() === LibraryFileAttribute::TYPE_BOOL) {
                throw new FilterQuerySemanticException(sprintf('Unexpected comma at position %d; attribute "%s" of type "%s" cannot be used with multiple values', $this->position(), $this->tmpAttributeName, $attribute->getType()));
            }

            $this->move();
            $this->valueIndex ++;
        }
    }

    private function assertLegalRelationalOperator()
    {
        $attribute = $this->getAttribute($this->tmpAttributeName);

        if (!array_key_exists($attribute->getType(), self::SUPPORTED_RELATIONAL_OPERATORS)) {
            throw new \RuntimeException(sprintf('Unknown attribute type "%s"', $attribute->getType()));
        }

        if (!in_array($this->tmpOperator, self::SUPPORTED_RELATIONAL_OPERATORS[$attribute->getType()])) {
            throw new FilterQuerySemanticException(sprintf(
                'Illegal relational operator "%s" for attribute "%s" of type "%s" at position %d',
                $this->tmpOperator,
                $this->tmpAttributeName,
                $attribute->getType(),
                $this->position
            ));
        }
    }

    private function load(string $str)
    {
        $this->position = 0;
        $this->chunks = strlen($str) > 0 ? str_split($str) : [];
        $this->openedParenthesis = [];
        $this->bucket = new TokenBucket();
        $this->expect = self::TOKEN_ATTRIBUTE_NAME;
        $this->tmpAttributeName = '';
        $this->tmpOperator = null;
        $this->tmpValues = [];
        $this->valueIndex = 0;
    }

    private function length(): int
    {
        return count($this->chunks);
    }

    private function current(): string
    {
        return $this->chunks[$this->position()];
    }

    private function prev():? string
    {
        $prev = $this->position() - 1;

        if ($prev >= 0) {
            return $this->chunks[$prev];
        }

        return null;
    }

    private function peek():? string
    {
        $next = $this->position() + 1;

        if ($next > $this->length()) {
            return null;
        }

        return $this->chunks[$next];
    }

    private function was(string $char): bool
    {
        return $this->prev() === $char;
    }

    private function is(string $char): bool
    {
        return $this->current() === $char;
    }

    private function will(string $char): bool
    {
        return $this->peek() === $char;
    }

    private function position(): int
    {
        return $this->position;
    }

    private function move()
    {
        $this->position ++;
    }

    private function done(): bool
    {
        return $this->length() <= $this->position;
    }

    private function isInParenthesis(): bool
    {
        return count($this->openedParenthesis) > 0;
    }

    private function token(AbstractToken $token)
    {
        $this->bucket->addToken($token);
    }

    private function expects(string $type): bool
    {
        return $type === $this->expect;
    }

    private function expect(string $type)
    {
        $this->expect = $type;
    }

    private function skipWhitespace()
    {
        // Ignore whitespace after values.
        while (!$this->done() && preg_match('/^\s/', $this->current())) {
            $this->move();
        }
    }
}
