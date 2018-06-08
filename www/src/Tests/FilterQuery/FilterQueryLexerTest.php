<?php

namespace App\Tests\FilterQuery;

use App\Exception\FilterQuerySemanticException;
use App\Exception\FilterQuerySyntaxException;
use App\FilterQuery\FilterQueryLexer;
use PHPUnit\Framework\TestCase;

class FilterQueryLexerTest extends TestCase
{
    /**
     * @var FilterQueryLexer
     */
    private $lexer;

    public function setUp()
    {
        $this->lexer = new FilterQueryLexer();
    }

    public function tearDown()
    {
        $this->lexer = null;
    }

    public function testEmptyFilter()
    {
        $this->assertEquals([], $this->lexer->read(''));
    }

    public function testOnlySpaceFilter()
    {
        $this->assertEquals([], $this->lexer->read(' '));
    }

    public function testLineBreakFilter()
    {
        $this->assertEquals([], $this->lexer->read("\n"));
    }

    public function testEqStr()
    {
        $this->assertEquals([[
            'attribute' => 'genre',
            'operator' => 'eq',
            'values' => ['Rock'],
        ]], $this->lexer->read('genre = Rock'));
    }

    public function testEqSingleQuotedStr()
    {
        $this->assertEquals([[
            'attribute' => 'genre',
            'operator' => 'eq',
            'values' => ['Hard Rock'],
        ]], $this->lexer->read(' genre = \'Hard Rock\' '));
    }

    public function testEqDoubleQuotedStr()
    {
        $this->assertEquals([[
            'attribute' => 'genre',
            'operator' => 'eq',
            'values' => ['Hard Rock'],
        ]], $this->lexer->read('genre = "Hard Rock"'));
    }

    public function testEqMultiQuotedStr()
    {
        $this->assertEquals([[
            'attribute' => 'genre',
            'operator' => 'eq',
            'values' => ['Hard Rock', 'Dance, Electronic', 'Metal', 'Rock\'n\'Roll'],
        ]], $this->lexer->read('genre = "Hard Rock", "Dance, Electronic", Metal, "Rock\'n\'Roll"'));
    }

    public function testEqMultiStr()
    {
        $this->assertEquals([[
            'attribute' => 'genre',
            'operator' => 'eq',
            'values' => ['Rock', 'Metal'],
        ]], $this->lexer->read('genre = Rock, Metal'));
    }

    public function testEqInt()
    {
        $this->assertEquals([[
            'attribute' => 'bpm',
            'operator' => 'eq',
            'values' => ['100'],
        ]], $this->lexer->read('bpm = 100'));
    }

    public function testEqFloat()
    {
        $this->assertEquals([[
            'attribute' => 'bpm',
            'operator' => 'eq',
            'values' => ['100.001'],
        ]], $this->lexer->read('bpm = 100.001'));
    }

    public function testNeqStr()
    {
        $this->assertEquals([[
            'attribute' => 'genre',
            'operator' => 'neq',
            'values' => ['Rock'],
        ]], $this->lexer->read('genre != Rock'));
    }

    public function testGtInt()
    {
        $this->assertEquals([[
            'attribute' => 'bpm',
            'operator' => 'gt',
            'values' => ['100'],
        ]], $this->lexer->read('bpm > 100'));
    }

    public function testGteInt()
    {
        $this->assertEquals([[
            'attribute' => 'bpm',
            'operator' => 'gte',
            'values' => ['100'],
        ]], $this->lexer->read('bpm >= 100'));
    }

    public function testLtInt()
    {
        $this->assertEquals([[
            'attribute' => 'bpm',
            'operator' => 'lt',
            'values' => ['100'],
        ]], $this->lexer->read('bpm < 100'));
    }

    public function testLteInt()
    {
        $this->assertEquals([[
            'attribute' => 'bpm',
            'operator' => 'lte',
            'values' => ['100'],
        ]], $this->lexer->read('bpm <= 100'));
    }

    public function testTwoAndConditions()
    {
        $this->assertEquals([
            [
                'attribute' => 'genre',
                'operator' => 'eq',
                'values' => ['Rock'],
            ],
            [
                'operator' => 'and',
            ],
            [
                'attribute' => 'bpm',
                'operator' => 'eq',
                'values' => ['100'],
            ],
        ], $this->lexer->read('genre = Rock & bpm = 100'));
    }

    public function testMultipleConditions()
    {
        $this->assertEquals([
            [
                'attribute' => 'genre',
                'operator' => 'eq',
                'values' => ['Rock'],
            ],
            [
                'operator' => 'or',
            ],
            [
                'attribute' => 'bpm',
                'operator' => 'eq',
                'values' => ['100'],
            ],
            [
                'operator' => 'and',
            ],
            [
                'attribute' => 'year',
                'operator' => 'lt',
                'values' => ['2018'],
            ],
        ], $this->lexer->read(
            'genre = Rock ' . PHP_EOL
            . ' | bpm = 100' . PHP_EOL
            . '& year < 2018'
        ));
    }

    public function testParenthesis()
    {
        $this->assertEquals([
            [
                'type' => 'parenthesis_open',
            ],
            [
                'attribute' => 'genre',
                'operator' => 'eq',
                'values' => ['Rock'],
            ],
            [
                'operator' => 'or',
            ],
            [
                'attribute' => 'genre',
                'operator' => 'eq',
                'values' => ['Hard Rock'],
            ],
            [
                'type' => 'parenthesis_closed',
            ],
            [
                'operator' => 'and',
            ],
            [
                'attribute' => 'bpm',
                'operator' => 'eq',
                'values' => ['100'],
            ],
        ], $this->lexer->read('(genre = Rock | genre = \'Hard Rock\') & bpm = 100'));
    }

    public function testMultipleParenthesis()
    {
        $this->assertEquals([
            [
                'type' => 'parenthesis_open',
            ],
            [
                'attribute' => 'genre',
                'operator' => 'eq',
                'values' => ['Rock'],
            ],
            [
                'operator' => 'or',
            ],
            [
                'type' => 'parenthesis_open',
            ],
            [
                'attribute' => 'genre',
                'operator' => 'eq',
                'values' => ['Hard Rock'],
            ],
            [
                'operator' => 'and',
            ],
            [
                'attribute' => 'year',
                'operator' => 'eq',
                'values' => ['2018'],
            ],
            [
                'type' => 'parenthesis_closed',
            ],
            [
                'type' => 'parenthesis_closed',
            ],
            [
                'operator' => 'and',
            ],
            [
                'attribute' => 'bpm',
                'operator' => 'eq',
                'values' => ['100'],
            ],
        ], $this->lexer->read('(genre = Rock | (genre = \'Hard Rock\' & year = 2018)) & bpm = 100'));
    }

    public function testMissingAttributeRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Expected attribute at position 1; got "="');

        $this->lexer->read(' = 100');
    }

    public function testMissingOperatorBetweenConditionsRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Unexpected "b" as position 13; expected logical operator');

        $this->lexer->read('genre = Rock bpm = 100');
    }

    public function testUnexpectedCommaRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Unexpected "," at position 6; expected relational operator');

        $this->lexer->read('genre ,');
    }

    public function testUnexpectedCommaAfterOperatorRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Value must be quoted if starting with "," at position 8');

        $this->lexer->read('genre = ,');
    }

    public function testUnexpectedCommaUsedInGtRaisesException()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Unexpected comma at position 9; relational operator "gt" cannot be used with multiple values');

        $this->lexer->read('bpm > 100, 101');
    }

    public function testUnexpectedCommaUsedInGteRaisesException()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Unexpected comma at position 10; relational operator "gte" cannot be used with multiple values');

        $this->lexer->read('bpm >= 100, 101');
    }

    public function testUnexpectedCommaUsedInLtRaisesException()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Unexpected comma at position 9; relational operator "lt" cannot be used with multiple values');

        $this->lexer->read('bpm < 100, 101');
    }

    public function testUnexpectedCommaUsedInLteRaisesException()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Unexpected comma at position 10; relational operator "lte" cannot be used with multiple values');

        $this->lexer->read('bpm <= 100, 101');
    }

    public function testUnclosedSingleQuoteRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Quote starting at position 8 is not closed');

        $this->lexer->read('genre = \'Hard Rock');
    }

    public function testUnclosedDoubleQuoteRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Quote starting at position 8 is not closed');

        $this->lexer->read('genre = "Hard Rock');
    }

    public function testMissingOperatorRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Unexpected "b" at position 6; expected relational operator');

        $this->lexer->read('genre bpm');
    }

    public function testMissingOpeningParenthesisRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Closing parenthesis at position 12 was not opened');

        $this->lexer->read('genre = Rock)');
    }

    public function testMissingClosingParenthesisRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Opening parenthesis at position 0 was not closed');

        $this->lexer->read('(genre = Rock');
    }

    public function testMissingClosedParenthesisRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Opening parenthesis at position 0 was not closed');

        $this->lexer->read('(genre = Rock | ( genre = "Hard Rock" & year = 2018)');
    }

    public function testParenthesisInsteadOfValueRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Value must be quoted if starting with "(" at position 8');

        $this->lexer->read('genre = (Rock & year = 2018)');
    }
}
