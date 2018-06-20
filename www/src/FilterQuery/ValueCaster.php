<?php

namespace App\FilterQuery;

use App\Entity\LibraryFileAttribute;
use App\Exception\FilterQuerySemanticException;

class ValueCaster
{
    public function castMulti(LibraryFileAttribute $attribute, array $values): array
    {
        $res = [];

        foreach ($values as $k => $v) {
            $res[$k] = $this->cast($attribute, $v);
        }

        return $res;
    }

    public function cast(LibraryFileAttribute $attribute, $v)
    {
        switch ($attribute->getType()) {
            case LibraryFileAttribute::TYPE_BOOL:
                if (is_bool($v)) {
                    return $v;
                } elseif ($v === 1 || $v === '1') {
                    return true;
                } elseif ($v === 0 || $v === '0') {
                    return false;
                }
                break;

            case LibraryFileAttribute::TYPE_DATE:
                if (is_scalar($v)) {
                    preg_match('/^(\d{4})(?:-(\d{1,2})){0,1}(?:-(\d{1,2})){0,1}$/', (string) $v, $matches);

                    if (isset($matches[1])) {
                        $year = $matches[1];
                        $month = 1;
                        $day = 1;

                        if (isset($matches[2])) {
                            $month = $matches[2];
                        }

                        if (isset($matches[3])) {
                            $day = $matches[3];
                        }

                        return sprintf('%d-%02d-%02d', $year, $month, $day);
                    }
                }
                break;

            case LibraryFileAttribute::TYPE_DATE_TIME:
                if (is_scalar($v)) {
                    preg_match(
                        '/^(\d{4})(?:-(\d{1,2})){0,1}(?:-(\d{1,2})){0,1}(?: (\d{1,2}):(\d{1,2})(?::(\d{1,2})){0,1}){0,1}$/', (string) $v, $matches);

                    if (isset($matches[1])) {
                        $year = $matches[1];
                        $month = 1;
                        $day = 1;
                        $hour = 0;
                        $minute = 0;
                        $second = 0;

                        if (isset($matches[2])) {
                            $month = $matches[2];
                        }

                        if (isset($matches[3])) {
                            $day = $matches[3];
                        }

                        if (isset($matches[4]) && isset($matches[5])) {
                            $hour = $matches[4];
                            $minute = $matches[5];

                            if (isset($matches[6])) {
                                $second = $matches[6];
                            }

                        }

                        return sprintf('%d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second);
                    }
                }
                break;

            case LibraryFileAttribute::TYPE_FLOAT:
                if (is_int($v) || ctype_digit($v) || (is_scalar($v) && preg_match('/^\d+\.*\d*$/', $v))) {
                    return $v;
                }
                break;

            case LibraryFileAttribute::TYPE_INT:
                if (is_int($v) || ctype_digit($v)) {
                    return (int) $v;
                }
                break;

            case LibraryFileAttribute::TYPE_STRING:
                if (is_scalar($v)) {
                    return (string) $v;
                }
                break;

            default:
                if (empty($attribute->getType())) {
                    throw new \RuntimeException(sprintf('%s has no type', LibraryFileAttribute::class));
                }

                throw new \RuntimeException(sprintf('%s has invalid type "%s"', LibraryFileAttribute::class, $attribute->getType()));
        }

        throw $this->createInvalidValueException($attribute, $v);
    }

    protected function createInvalidValueException(LibraryFileAttribute $attribute, $v)
    {
        if (is_scalar($v)) {
            return new FilterQuerySemanticException(sprintf(
                'Value (%s) "%s" is invalid for attribute "%s" of type "%s"',
                gettype($v),
                $v,
                $attribute->getName(),
                $attribute->getType()
            ));
        }

        return new FilterQuerySemanticException(sprintf(
            'Value of type %s is invalid for attribute "%s" of type "%s"',
            gettype($v),
            $attribute->getName(),
            $attribute->getType()
        ));
    }
}