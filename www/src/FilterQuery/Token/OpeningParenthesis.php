<?php

namespace App\FilterQuery\Token;

use App\FilterQuery\FilterQueryLexer;

class OpeningParenthesis extends AbstractToken
{
    public function __construct()
    {
        parent::__construct(FilterQueryLexer::TOKEN_PARENTHESIS_OPENING);
    }
}
