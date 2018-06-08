<?php

namespace App\FilterQuery;

use App\Exception\FilterQuerySemanticException;
use App\Exception\FilterQuerySyntaxException;

class FilterQueryLexer
{
    const PATTERN_ATTRIBUTE_CHAR = '/^[a-z0-9_]$/i';
    const RELATIONAL_OPERATOR = 'relational_operator';
    const RELATIONAL_OPERATOR_EQUAL = 'eq';
    const RELATIONAL_OPERATOR_NOT_EQUAL = 'neq';
    const RELATIONAL_OPERATOR_CONTAINS = 'con';
    const RELATIONAL_OPERATOR_NOT_CONTAINS = 'ncon';
    const RELATIONAL_OPERATOR_GREATER = 'gt';
    const RELATIONAL_OPERATOR_GREATER_EQUAL = 'gte';
    const RELATIONAL_OPERATOR_LESS = 'lt';
    const RELATIONAL_OPERATOR_LESS_EQUAL = 'lte';
    const LOGICAL_OPERATOR = 'logical_operator';
    const LOGICAL_OPERATOR_AND = 'and';
    const LOGICAL_OPERATOR_OR = 'or';
    const ATTRIBUTE_FILTER = 'attribute_filter';
    const ATTRIBUTE = 'attribute';
    const VALUE = 'value';

    public function read(string $qry): array
    {
        if (strlen($qry) === 0) {
            return [];
        }

        $res = [];
        $chunks = str_split($qry);
        $len = count($chunks);
        $pos = 0;

        $expect = self::ATTRIBUTE;
        $index = 0;
        $valueIndex = 0;
        $openedParenthesis = [];

        while ($pos < $len) {
            // Skip all not quoted whitespaces.
            while (preg_match('/^\s/', $chunks[$pos])) {
                $pos ++;

                if (!isset($chunks[$pos])) {
                    // Consumed all whitespaces at the end of $qry.
                    break 2;
                }
            }

            if ($expect === self::LOGICAL_OPERATOR) {
                if ($chunks[$pos] === ')') {
                    if (count($openedParenthesis) === 0) {
                        throw new FilterQuerySyntaxException(sprintf('Closing parenthesis at position %d was not opened', $pos));
                    }

                    $index ++;
                    $res[$index] = [
                        'type' => 'parenthesis_closed',
                    ];
                    $pos ++;
                    array_pop($openedParenthesis);
                } elseif ($chunks[$pos] === '&') {
                    $index ++;
                    $res[$index]['operator'] = self::LOGICAL_OPERATOR_AND;
                    $expect = 'attribute';
                    $pos ++;
                    $index ++;
                } elseif ($chunks[$pos] === '|') {
                    $index ++;
                    $res[$index]['operator'] = self::LOGICAL_OPERATOR_OR;
                    $expect = self::ATTRIBUTE;
                    $pos ++;
                    $index ++;
                } else {
                    throw new FilterQuerySyntaxException(sprintf('Unexpected "%s" as position %d; expected logical operator', $chunks[$pos], $pos));
                }
            }

            elseif ($expect === self::ATTRIBUTE) {
                if ($chunks[$pos] === '(') {
                    $res[$index] = [
                        'type' => 'parenthesis_open',
                    ];
                    $openedParenthesis[] = $pos;
                    $pos ++;
                    $index ++;
                    continue;
                }


                if (!preg_match(self::PATTERN_ATTRIBUTE_CHAR, $chunks[$pos])) {
                    throw new FilterQuerySyntaxException(sprintf('Expected attribute at position %d; got "%s"', $pos, $chunks[$pos]));
                }

                if (!isset($res[$index])) {
                    $res[$index] = [
                        'attribute' => null,
                        'operator' => null,
                        'values' => [],
                    ];
                }

                // Consume all attribute chars.
                while (preg_match(self::PATTERN_ATTRIBUTE_CHAR, $chunks[$pos])) {
                    $res[$index]['attribute'] .= $chunks[$pos];
                    $pos ++;
                }

                $expect = self::RELATIONAL_OPERATOR;
            }

            elseif ($expect === self::RELATIONAL_OPERATOR) {
                if ($chunks[$pos] === '!') {
                    if (isset($chunks[$pos + 1]) && $chunks[$pos + 1] === '=') {
                        $res[$index]['operator'] = self::RELATIONAL_OPERATOR_NOT_EQUAL;
                        $pos ++;
                    } elseif (isset($chunks[$pos + 1]) && $chunks[$pos + 1] === '~') {
                        $res[$index]['operator'] = self::RELATIONAL_OPERATOR_NOT_CONTAINS;
                        $pos ++;
                    } else {
                        throw new FilterQuerySyntaxException(sprintf('Unexpected "!" at position %d', $pos));
                    }
                } elseif ($chunks[$pos] === '=') {
                    $res[$index]['operator'] = self::RELATIONAL_OPERATOR_EQUAL;
                } elseif ($chunks[$pos] === '~') {
                    $res[$index]['operator'] = self::RELATIONAL_OPERATOR_CONTAINS;
                } elseif ($chunks[$pos] === '<') {
                    if (isset($chunks[$pos + 1]) && $chunks[$pos + 1] === '=') {
                        $res[$index]['operator'] = self::RELATIONAL_OPERATOR_LESS_EQUAL;
                        $pos ++;
                    } else {
                        $res[$index]['operator'] = self::RELATIONAL_OPERATOR_LESS;
                    }
                } elseif ($chunks[$pos] === '>') {
                    if (isset($chunks[$pos + 1]) && $chunks[$pos + 1] === '=') {
                        $res[$index]['operator'] = self::RELATIONAL_OPERATOR_GREATER_EQUAL;
                        $pos ++;
                    } else {
                        $res[$index]['operator'] = self::RELATIONAL_OPERATOR_GREATER;
                    }
                } else {
                    throw new FilterQuerySyntaxException(sprintf('Unexpected "%s" at position %d; expected relational operator', $chunks[$pos], $pos));
                }

                $pos ++;
                $expect = self::VALUE;
            }

            elseif ($expect === self::VALUE) {
                if ($chunks[$pos] === '"' || $chunks[$pos] === '\'') {
                    $quote = $chunks[$pos];
                    $quotePos = $pos;
                    // Consume all chars until the quote gets closed.
                    while (true) {
                        $pos ++;

                        if (!isset($chunks[$pos])) {
                            // Reached the end of $qry before the quote is closed.
                            throw new FilterQuerySyntaxException(sprintf('Quote starting at position %d is not closed', $quotePos));
                        }

                        if ($chunks[$pos] === $quote) {
                            // Found the end of the quote. Stop consuming chars.
                            break;
                        }

                        if (!isset($res[$index]['values'][$valueIndex])) {
                            $res[$index]['values'][$valueIndex] = '';
                        }

                        $res[$index]['values'][$valueIndex] .= $chunks[$pos];
                    }

                    $pos ++;
                } elseif (!preg_match('/^\w$/', $chunks[$pos])) {
                    throw new FilterQuerySyntaxException(sprintf('Value must be quoted if starting with "%s" at position %d', $chunks[$pos], $pos));
                } else {
                    // Consume all chars of a non-quoted value.
                    while (isset($chunks[$pos]) && preg_match('/^[\w.]$/', $chunks[$pos])) {
                        if (!isset($res[$index]['values'][$valueIndex])) {
                            $res[$index]['values'][$valueIndex] = '';
                        }

                        $res[$index]['values'][$valueIndex] .= $chunks[$pos];
                        $pos ++;
                    }
                }

                // Ignore whitespace after values.
                while (isset($chunks[$pos]) && preg_match('/^\s/', $chunks[$pos])) {
                    $pos ++;
                }

                // Check if an enumeration is indicated.
                if (!isset($chunks[$pos]) || $chunks[$pos] !== ',') {
                    $expect = self::LOGICAL_OPERATOR;
                    $valueIndex = 0;
                } else {
                    if (!in_array($res[$index]['operator'], ['eq', 'neq', 'con', 'ncon'])) {
                        // The defined relational operator cannot not be used with multiple values.
                        throw new FilterQuerySemanticException(sprintf('Unexpected comma at position %d; relational operator "%s" cannot be used with multiple values', $pos, $res[$index]['operator']));
                    }

                    $pos ++;
                    $valueIndex ++;
                }
            }

            else {
                // WTF happened?!
                throw new \RuntimeException(sprintf('Did not expect that "%s" was expected at position %d', $expect, $pos));
            }
        }

        if (count($openedParenthesis) > 0) {
            throw new FilterQuerySyntaxException(sprintf('Opening parenthesis at position %d was not closed', array_pop($openedParenthesis)));
        }

        return $res;
    }
}
