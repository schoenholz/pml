<?php

namespace App\Tests\FilterQuery\FilterQueryLexer;

use App\Exception\FilterQuerySemanticException;

class SemanticErrorTest extends AbstractFilterQueryLexerTest
{
    public function testUnexpectedCommaUsedInGtRaisesException()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Unexpected comma at position 9; relational operator "RELATIONAL_OPERATOR_GREATER" cannot be used with multiple values');

        $this->lexer->parse('bpm > 100, 101');
    }

    public function testUnexpectedCommaUsedInGteRaisesException()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Unexpected comma at position 10; relational operator "RELATIONAL_OPERATOR_GREATER_EQUAL" cannot be used with multiple values');

        $this->lexer->parse('bpm >= 100, 101');
    }

    public function testUnexpectedCommaUsedInLtRaisesException()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Unexpected comma at position 9; relational operator "RELATIONAL_OPERATOR_LESS" cannot be used with multiple values');

        $this->lexer->parse('bpm < 100, 101');
    }

    public function testUnexpectedCommaUsedInLteRaisesException()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Unexpected comma at position 10; relational operator "RELATIONAL_OPERATOR_LESS_EQUAL" cannot be used with multiple values');

        $this->lexer->parse('bpm <= 100, 101');
    }

    public function testMultiValueEqForBoolAttributeRaisesException()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Unexpected comma at position 16; attribute "is_favourite" of type "bool" cannot be used with multiple values');

        $this->lexer->parse('is_favourite = 1, 0');
    }

    public function testMultiValueNeqForBoolAttributeRaisesException()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Unexpected comma at position 17; attribute "is_favourite" of type "bool" cannot be used with multiple values');

        $this->lexer->parse('is_favourite != 1, 0');
    }

    public function testInvalidValueRaisesException()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Value (string) "Rock" is invalid for attribute "year" of type "int"');

        $this->lexer->parse('year = Rock');
    }

    public function testGreaterOperatorRaisesExceptionForBoolAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_GREATER" for attribute "is_favourite" of type "bool"');

        $this->lexer->parse('is_favourite > 1');
    }

    public function testGreaterEqualOperatorRaisesExceptionForBoolAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_GREATER_EQUAL" for attribute "is_favourite" of type "bool"');

        $this->lexer->parse('is_favourite >= 1');
    }

    public function testLessOperatorRaisesExceptionForBoolAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_LESS" for attribute "is_favourite" of type "bool"');

        $this->lexer->parse('is_favourite < 1');
    }

    public function testLessEqualOperatorRaisesExceptionForBoolAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_LESS_EQUAL" for attribute "is_favourite" of type "bool"');

        $this->lexer->parse('is_favourite <= 1');
    }

    public function testContainsOperatorRaisesExceptionForBoolAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_CONTAINS" for attribute "is_favourite" of type "bool"');

        $this->lexer->parse('is_favourite ~ 1');
    }

    public function testNotContainsOperatorRaisesExceptionForBoolAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_NOT_CONTAINS" for attribute "is_favourite" of type "bool"');

        $this->lexer->parse('is_favourite !~ 1');
    }

    public function testContainsOperatorRaisesExceptionForDateAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_CONTAINS" for attribute "release_date" of type "date"');

        $this->lexer->parse('release_date ~ 2018');
    }

    public function testNotContainsOperatorRaisesExceptionForDateAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_NOT_CONTAINS" for attribute "release_date" of type "date"');

        $this->lexer->parse('release_date !~ 2018');
    }

    public function testContainsOperatorRaisesExceptionForDateTimeAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_CONTAINS" for attribute "release_time" of type "date_time"');

        $this->lexer->parse('release_time ~ 2018');
    }

    public function testNotContainsOperatorRaisesExceptionForDateTimeAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_NOT_CONTAINS" for attribute "release_time" of type "date_time"');

        $this->lexer->parse('release_time !~ 2018');
    }

    public function testContainsOperatorRaisesExceptionForFloatAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_CONTAINS" for attribute "bpm" of type "float"');

        $this->lexer->parse('bpm ~ 100');
    }

    public function testNotContainsOperatorRaisesExceptionForFloatAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_NOT_CONTAINS" for attribute "bpm" of type "float"');

        $this->lexer->parse('bpm !~ 100');
    }

    public function testContainsOperatorRaisesExceptionForIntAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_CONTAINS" for attribute "year" of type "int"');

        $this->lexer->parse('year ~ 2018');
    }

    public function testNotContainsOperatorRaisesExceptionForIntAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_NOT_CONTAINS" for attribute "year" of type "int"');

        $this->lexer->parse('year !~ 2018');
    }

    public function testGreaterOperatorRaisesExceptionForStringAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_GREATER" for attribute "genre" of type "string"');

        $this->lexer->parse('genre > 2018');
    }

    public function testGreaterEqualOperatorRaisesExceptionForStringAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_GREATER_EQUAL" for attribute "genre" of type "string"');

        $this->lexer->parse('genre >= 2018');
    }

    public function testLessOperatorRaisesExceptionForStringAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_LESS" for attribute "genre" of type "string"');

        $this->lexer->parse('genre < 2018');
    }

    public function testLessEqualOperatorRaisesExceptionForStringAttribute()
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage('Illegal relational operator "RELATIONAL_OPERATOR_LESS_EQUAL" for attribute "genre" of type "string"');

        $this->lexer->parse('genre <= 2018');
    }
}
