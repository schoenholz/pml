<?php

namespace App\Tests\FilterQuery\FilterQueryLexer;

class ParserTest extends AbstractFilterQueryLexerTest
{
    public function testEqStr()
    {
        $bucket = $this->lexer->parse('genre = Rock');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('genre', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['Rock'], $fragment1->getValues());
    }

    public function testEqSingleQuotedStr()
    {
        $bucket = $this->lexer->parse(' genre = \'Hard Rock\' ');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('genre', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['Hard Rock'], $fragment1->getValues());
    }

    public function testEqSingleQuotedStrWithEscapedQuote()
    {
        $bucket = $this->lexer->parse('genre = \'D\\\'n\\\'B\'');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('genre', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['D\'n\'B'], $fragment1->getValues());
    }

    public function testEqDoubleQuotedStr()
    {
        $bucket = $this->lexer->parse('genre = "Hard Rock "');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('genre', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['Hard Rock '], $fragment1->getValues());
    }

    public function testEqDoubleQuotedStrWithEscapedQuote()
    {
        $bucket = $this->lexer->parse('genre = "foo\"bar"');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('genre', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['foo"bar'], $fragment1->getValues());
    }

    public function testEqMultiQuotedStr()
    {
        $bucket = $this->lexer->parse('genre = "Hard Rock", "Dance, Electronic", Metal, "Rock\'n\'Roll"');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('genre', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['Hard Rock', 'Dance, Electronic', 'Metal', 'Rock\'n\'Roll'], $fragment1->getValues());
    }

    public function testEqMultiStr()
    {
        $bucket = $this->lexer->parse('genre = Rock , Metal');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('genre', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['Rock', 'Metal'], $fragment1->getValues());
    }

    public function testEqInt()
    {
        $bucket = $this->lexer->parse('bpm = 100 ');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('bpm', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['100'], $fragment1->getValues());
    }

    public function testEqFloat()
    {
        $bucket = $this->lexer->parse('bpm = 100.001');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('bpm', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['100.001'], $fragment1->getValues());
    }

    public function testNeqStr()
    {
        $bucket = $this->lexer->parse('genre != Rock');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('genre', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_NOT_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['Rock'], $fragment1->getValues());
    }

    public function testGtInt()
    {
        $bucket = $this->lexer->parse('bpm > 100');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('bpm', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_GREATER', $fragment1->getOperator());
        $this->assertEquals(['100'], $fragment1->getValues());
    }

    public function testGteInt()
    {
        $bucket = $this->lexer->parse('bpm >= 100');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('bpm', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_GREATER_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['100'], $fragment1->getValues());
    }

    public function testLtInt()
    {
        $bucket = $this->lexer->parse('bpm < 100');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('bpm', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_LESS', $fragment1->getOperator());
        $this->assertEquals(['100'], $fragment1->getValues());
    }

    public function testLteInt()
    {
        $bucket = $this->lexer->parse('bpm <= 100');
        $this->assertCount(1, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('bpm', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_LESS_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['100'], $fragment1->getValues());
    }

    public function testTwoAndConditions()
    {
        $bucket = $this->lexer->parse('genre = Rock & bpm = 100');
        $this->assertCount(3, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('genre', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['Rock'], $fragment1->getValues());

        $bucket->next();
        /** @var \App\FilterQuery\Token\LogicalOperator $fragment2 */
        $fragment2 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\LogicalOperator::class, $fragment2);
        $this->assertEquals('LOGICAL_OPERATOR_AND', $fragment2->getOperator());

        $bucket->next();
        /** @var \App\FilterQuery\Token\AttributeFilter $fragment3 */
        $fragment3 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment3);
        $this->assertEquals('bpm', $fragment3->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment3->getOperator());
        $this->assertEquals(['100'], $fragment3->getValues());
    }

    public function testMultipleConditions()
    {
        $bucket = $this->lexer->parse(
            'genre = Rock ' . PHP_EOL
            . ' | bpm = 100' . PHP_EOL
            . '& year < 2018'
        );
        $this->assertCount(5, $bucket);

        /** @var \App\FilterQuery\Token\AttributeFilter $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment1);
        $this->assertEquals('genre', $fragment1->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment1->getOperator());
        $this->assertEquals(['Rock'], $fragment1->getValues());

        $bucket->next();
        /** @var \App\FilterQuery\Token\LogicalOperator $fragment2 */
        $fragment2 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\LogicalOperator::class, $fragment2);
        $this->assertEquals('LOGICAL_OPERATOR_OR', $fragment2->getOperator());

        $bucket->next();
        /** @var \App\FilterQuery\Token\AttributeFilter $fragment3 */
        $fragment3 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment3);
        $this->assertEquals('bpm', $fragment3->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment3->getOperator());
        $this->assertEquals(['100'], $fragment3->getValues());

        $bucket->next();
        /** @var \App\FilterQuery\Token\LogicalOperator $fragment4 */
        $fragment4 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\LogicalOperator::class, $fragment4);
        $this->assertEquals('LOGICAL_OPERATOR_AND', $fragment4->getOperator());

        $bucket->next();
        /** @var \App\FilterQuery\Token\AttributeFilter $fragment5 */
        $fragment5 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment5);
        $this->assertEquals('year', $fragment5->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_LESS', $fragment5->getOperator());
        $this->assertEquals(['2018'], $fragment5->getValues());
    }

    public function testParenthesis()
    {
        $bucket = $this->lexer->parse('(genre = Rock | genre = \'Hard Rock\') & bpm = 100');
        $this->assertCount(7, $bucket);

        /** @var \App\FilterQuery\Token\OpeningParenthesis $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\OpeningParenthesis::class, $fragment1);

        $bucket->next();
        /** @var \App\FilterQuery\Token\AttributeFilter $fragment2 */
        $fragment2 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment2);
        $this->assertEquals('genre', $fragment2->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment2->getOperator());
        $this->assertEquals(['Rock'], $fragment2->getValues());

        $bucket->next();
        /** @var \App\FilterQuery\Token\LogicalOperator $fragment3 */
        $fragment3 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\LogicalOperator::class, $fragment3);
        $this->assertEquals('LOGICAL_OPERATOR_OR', $fragment3->getOperator());

        $bucket->next();
        /** @var \App\FilterQuery\Token\AttributeFilter $fragment4 */
        $fragment4 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment4);
        $this->assertEquals('genre', $fragment4->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment4->getOperator());
        $this->assertEquals(['Hard Rock'], $fragment4->getValues());

        $bucket->next();
        /** @var \App\FilterQuery\Token\ClosingParenthesis $fragment5 */
        $fragment5 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\ClosingParenthesis::class, $fragment5);

        $bucket->next();
        /** @var \App\FilterQuery\Token\LogicalOperator $fragment6 */
        $fragment6 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\LogicalOperator::class, $fragment6);
        $this->assertEquals('LOGICAL_OPERATOR_AND', $fragment6->getOperator());

        $bucket->next();
        /** @var \App\FilterQuery\Token\AttributeFilter $fragment7 */
        $fragment7 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment7);
        $this->assertEquals('bpm', $fragment7->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment7->getOperator());
        $this->assertEquals(['100'], $fragment7->getValues());
    }

    public function testNestedParenthesis()
    {
        $bucket = $this->lexer->parse('(genre = Rock | (genre = \'Hard Rock\' & year = 2018)) & bpm = 100');
        $this->assertCount(11, $bucket);

        /** @var \App\FilterQuery\Token\OpeningParenthesis $fragment1 */
        $fragment1 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\OpeningParenthesis::class, $fragment1);

        $bucket->next();
        /** @var \App\FilterQuery\Token\AttributeFilter $fragment2 */
        $fragment2 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment2);
        $this->assertEquals('genre', $fragment2->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment2->getOperator());
        $this->assertEquals(['Rock'], $fragment2->getValues());

        $bucket->next();
        /** @var \App\FilterQuery\Token\LogicalOperator $fragment3 */
        $fragment3 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\LogicalOperator::class, $fragment3);
        $this->assertEquals('LOGICAL_OPERATOR_OR', $fragment3->getOperator());

        $bucket->next();
        /** @var \App\FilterQuery\Token\OpeningParenthesis $fragment4 */
        $fragment4 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\OpeningParenthesis::class, $fragment4);

        $bucket->next();
        /** @var \App\FilterQuery\Token\AttributeFilter $fragment5 */
        $fragment5 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment5);
        $this->assertEquals('genre', $fragment5->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment5->getOperator());
        $this->assertEquals(['Hard Rock'], $fragment5->getValues());

        $bucket->next();
        /** @var \App\FilterQuery\Token\LogicalOperator $fragment6 */
        $fragment6 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\LogicalOperator::class, $fragment6);
        $this->assertEquals('LOGICAL_OPERATOR_AND', $fragment6->getOperator());

        $bucket->next();
        /** @var \App\FilterQuery\Token\AttributeFilter $fragment7 */
        $fragment7 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment7);
        $this->assertEquals('year', $fragment7->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment7->getOperator());
        $this->assertEquals(['2018'], $fragment7->getValues());

        $bucket->next();
        /** @var \App\FilterQuery\Token\ClosingParenthesis $fragment8 */
        $fragment8 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\ClosingParenthesis::class, $fragment8);

        $bucket->next();
        /** @var \App\FilterQuery\Token\ClosingParenthesis $fragment9 */
        $fragment9 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\ClosingParenthesis::class, $fragment9);

        $bucket->next();
        /** @var \App\FilterQuery\Token\LogicalOperator $fragment10 */
        $fragment10 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\LogicalOperator::class, $fragment10);
        $this->assertEquals('LOGICAL_OPERATOR_AND', $fragment10->getOperator());

        $bucket->next();
        /** @var \App\FilterQuery\Token\AttributeFilter $fragment11 */
        $fragment11 = $bucket->current();
        $this->assertInstanceOf(\App\FilterQuery\Token\AttributeFilter::class, $fragment11);
        $this->assertEquals('bpm', $fragment11->getAttribute());
        $this->assertEquals('RELATIONAL_OPERATOR_EQUAL', $fragment11->getOperator());
        $this->assertEquals(['100'], $fragment11->getValues());
    }
}
