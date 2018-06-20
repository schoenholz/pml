<?php

namespace App\Tests\FilterQuery\FilterQueryLexer;

class EmptyQueryTest extends AbstractFilterQueryLexerTest
{
    public function testEmptyFilter()
    {
        $bucket = $this->lexer->parse('');
        $this->assertCount(0, $bucket);
    }

    public function testOnlySpaceFilter()
    {
        $bucket = $this->lexer->parse(' ');
        $this->assertCount(0, $bucket);
    }

    public function testLineBreakFilter()
    {
        $bucket = $this->lexer->parse("\n");
        $this->assertCount(0, $bucket);
    }
}
