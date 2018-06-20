<?php

namespace App\Tests\FilterQuery\FilterQueryLexer;

use App\Exception\FilterQuerySyntaxException;

class SyntaxErrorTest extends AbstractFilterQueryLexerTest
{
    public function testMissingAttributeRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Expected attribute at position 1; got "="');

        $this->lexer->parse(' = 100');
    }

    public function testMissingOperatorBetweenConditionsRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Unexpected "b" as position 13; expected logical operator');

        $this->lexer->parse('genre = Rock bpm = 100');
    }

    public function testUnexpectedCommaRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Unexpected "," at position 6; expected relational operator');

        $this->lexer->parse('genre ,');
    }

    public function testUnexpectedCommaAfterOperatorRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Value must be quoted if starting with "," at position 8');

        $this->lexer->parse('genre = ,');
    }

    public function testUnclosedSingleQuoteRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Quote starting at position 8 is not closed');

        $this->lexer->parse('genre = \'Hard Rock');
    }

    public function testUnclosedDoubleQuoteRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Quote starting at position 8 is not closed');

        $this->lexer->parse('genre = "Hard Rock');
    }

    public function testMissingOperatorRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Unexpected "b" at position 6; expected relational operator');

        $this->lexer->parse('genre bpm');
    }

    public function testMissingOpeningParenthesisRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Closing parenthesis at position 12 was not opened');

        $this->lexer->parse('genre = Rock)');
    }

    public function testMissingClosingParenthesisRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Opening parenthesis at position 0 was not closed');

        $this->lexer->parse('(genre = Rock');
    }

    public function testOneMissingClosingParenthesisRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Opening parenthesis at position 0 was not closed');

        $this->lexer->parse('(genre = Rock | ( genre = "Hard Rock" & year = 2018)');
    }

    public function testParenthesisInsteadOfValueRaisesException()
    {
        $this->expectException(FilterQuerySyntaxException::class);
        $this->expectExceptionMessage('Value must be quoted if starting with "(" at position 8');

        $this->lexer->parse('genre = (Rock & year = 2018)');
    }
}
