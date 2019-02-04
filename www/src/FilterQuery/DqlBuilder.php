<?php

namespace App\FilterQuery;

use App\Entity\LibraryFileAttribute;
use App\Exception\FilterQuerySemanticException;
use App\FilterQuery\Token\AbstractToken;
use App\FilterQuery\Token\AttributeFilter;
use App\FilterQuery\Token\ClosingParenthesis;
use App\FilterQuery\Token\LogicalOperator;
use App\FilterQuery\Token\OpeningParenthesis;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\RuntimeException;

// todo genre = "" -> genre IS NULL or genre = '' or NOT EXISTS ?   OR   genre ? (<- isset) / genre !? (<- !isset)
class DqlBuilder
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LibraryFileAttribute[]
     */
    private $libraryFileAttributes;

    /**
     * @var int
     */
    private $attributeValueIndex = 0;

    /**
     * @var int
     */
    private $valueIndex = 0;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function build(AbstractToken $token)
    {
        if ($token instanceof AttributeFilter) {
            return $this->buildAttributeFilter($token);
        } elseif ($token instanceof ClosingParenthesis) {
            return $this->buildClosingParenthesis();
        } elseif ($token instanceof LogicalOperator) {
            return $this->buildLogicalOperator($token);
        } elseif ($token instanceof OpeningParenthesis) {
            return $this->buildOpeningParenthesis();
        }

        throw new RuntimeException(sprintf('Unknown token class "%s"', get_class($token)));
    }

    protected function getAttribute(string $attributeName): LibraryFileAttribute
    {
        $this->loadLibraryFileAttributes();

        if (!array_key_exists($attributeName, $this->libraryFileAttributes)) {
            throw new FilterQuerySemanticException(sprintf('Unknown attribute "%s"', $attributeName));
        }

        return $this->libraryFileAttributes[$attributeName];
    }

    private function buildAttributeFilter(AttributeFilter $token): DqlPart
    {
        $vs = $token->getValues();

        if (count($vs) === 0) {
            throw new FilterQuerySemanticException(sprintf('Attribute filter for attribute "%s" requires at least one value', $token->getAttribute()));
        }

        $attribute = $this->getAttribute($token->getAttribute());

        switch ($token->getOperator()) {
            case FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL:
                $this->attributeValueIndex ++;
                $this->valueIndex ++;

                if (count($vs) === 1) {
                    return new DqlPart(sprintf(
                        'EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND lfav_%d.%s = :value_%d)',
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $attribute->getValueFieldName(),
                        $this->valueIndex
                    ), [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                        sprintf('value_%d', $this->valueIndex) => reset($vs),
                    ]);
                } else {
                    return new DqlPart(sprintf(
                        'EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND lfav_%d.%s IN(:value_%d))',
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $attribute->getValueFieldName(),
                        $this->valueIndex
                    ), [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                        sprintf('value_%d', $this->valueIndex) => $vs,
                    ]);
                }
                break;

            case FilterQueryLexer::RELATIONAL_OPERATOR_NOT_EQUAL:
                $this->attributeValueIndex ++;
                $this->valueIndex ++;

                if (count($vs) === 1) {
                    return new DqlPart(sprintf(
                        'NOT EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND lfav_%d.%s = :value_%d)',
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $attribute->getValueFieldName(),
                        $this->valueIndex
                    ), [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                        sprintf('value_%d', $this->valueIndex) => reset($vs),
                    ]);
                } else {
                    return new DqlPart(sprintf(
                        'NOT EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND lfav_%d.%s IN(:value_%d))',
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $attribute->getValueFieldName(),
                        $this->valueIndex
                    ), [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                        sprintf('value_%d', $this->valueIndex) => $vs,
                    ]);
                }
                break;

            case FilterQueryLexer::RELATIONAL_OPERATOR_CONTAINS:
                $this->attributeValueIndex ++;

                if (count($vs) === 1) {
                    $this->valueIndex ++;

                    return new DqlPart(sprintf(
                        'EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND lfav_%d.%s LIKE :value_%d)',
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $attribute->getValueFieldName(),
                        $this->valueIndex
                    ), [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                        sprintf('value_%d', $this->valueIndex) => '%' . trim(reset($vs), '%') . '%',
                    ]);
                } else {
                    $format = 'EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND (';
                    $params = [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                    ];
                    $args = [
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                    ];

                    $valueCond = [];
                    foreach ($vs as $v) {
                        $this->valueIndex ++;
                        $valueCond[] = 'lfav_%d.%s LIKE :value_%d';
                        $args[] = $this->attributeValueIndex;
                        $args[] = $attribute->getValueFieldName();
                        $args[] = $this->valueIndex;

                        $params[sprintf('value_%d', $this->valueIndex)] = '%' . trim($v, '%') . '%';
                    }

                    $format .= implode(' OR ', $valueCond);
                    $format .= '))';

                    return new DqlPart(vsprintf($format, $args), $params);
                }
                break;

            case FilterQueryLexer::RELATIONAL_OPERATOR_NOT_CONTAINS:
                $this->attributeValueIndex ++;

                if (count($vs) === 1) {
                    $this->valueIndex ++;

                    return new DqlPart(sprintf(
                        'NOT EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND lfav_%d.%s LIKE :value_%d)',
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $attribute->getValueFieldName(),
                        $this->valueIndex
                    ), [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                        sprintf('value_%d', $this->valueIndex) => '%' . trim(reset($vs), '%') . '%',
                    ]);
                } else {
                    $format = 'NOT EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND (';
                    $params = [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                    ];
                    $args = [
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                    ];

                    $valueCond = [];
                    foreach ($vs as $v) {
                        $this->valueIndex ++;
                        $valueCond[] = 'lfav_%d.%s LIKE :value_%d';
                        $args[] = $this->attributeValueIndex;
                        $args[] = $attribute->getValueFieldName();
                        $args[] = $this->valueIndex;

                        $params[sprintf('value_%d', $this->valueIndex)] = '%' . trim($v, '%') . '%';
                    }

                    $format .= implode(' OR ', $valueCond);
                    $format .= '))';

                    return new DqlPart(vsprintf($format, $args), $params);
                }
                break;

            case FilterQueryLexer::RELATIONAL_OPERATOR_GREATER:
                if (count($vs) === 1) {
                    $this->attributeValueIndex ++;
                    $this->valueIndex ++;

                    return new DqlPart(sprintf(
                        'EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND lfav_%d.%s > :value_%d)',
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $attribute->getValueFieldName(),
                        $this->valueIndex
                    ), [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                        sprintf('value_%d', $this->valueIndex) => reset($vs),
                    ]);
                } else {
                    throw new \RuntimeException(sprintf('Relational operator "%s" cannot be used with multiple values', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER));
                }
                break;

            case FilterQueryLexer::RELATIONAL_OPERATOR_GREATER_EQUAL:
                if (count($vs) === 1) {
                    $this->attributeValueIndex ++;
                    $this->valueIndex ++;

                    return new DqlPart(sprintf(
                        'EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND lfav_%d.%s >= :value_%d)',
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $attribute->getValueFieldName(),
                        $this->valueIndex
                    ), [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                        sprintf('value_%d', $this->valueIndex) => reset($vs),
                    ]);
                } else {
                    throw new \RuntimeException(sprintf('Relational operator "%s" cannot be used with multiple values', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER));
                }
                break;

            case FilterQueryLexer::RELATIONAL_OPERATOR_LESS:
                if (count($vs) === 1) {
                    $this->attributeValueIndex ++;
                    $this->valueIndex ++;

                    return new DqlPart(sprintf(
                        'EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND lfav_%d.%s < :value_%d)',
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $attribute->getValueFieldName(),
                        $this->valueIndex
                    ), [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                        sprintf('value_%d', $this->valueIndex) => reset($vs),
                    ]);
                } else {
                    throw new \RuntimeException(sprintf('Relational operator "%s" cannot be used with multiple values', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER));
                }
                break;

            case FilterQueryLexer::RELATIONAL_OPERATOR_LESS_EQUAL:
                if (count($vs) === 1) {
                    $this->attributeValueIndex ++;
                    $this->valueIndex ++;

                    return new DqlPart(sprintf(
                        'EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_%d WHERE lfav_%d.libraryFile = lf.id AND lfav_%d.libraryFileAttribute = :attribute_%d AND lfav_%d.%s <= :value_%d)',
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $this->attributeValueIndex,
                        $attribute->getValueFieldName(),
                        $this->valueIndex
                    ), [
                        sprintf('attribute_%d', $this->attributeValueIndex) => $attribute,
                        sprintf('value_%d', $this->valueIndex) => reset($vs),
                    ]);
                } else {
                    throw new \RuntimeException(sprintf('Relational operator "%s" cannot be used with multiple values', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER));
                }
                break;
        }

        throw new \Exception('Not implemented'); // todo
    }

    private function buildClosingParenthesis(): DqlPart
    {
        return new DqlPart(')');
    }

    private function buildLogicalOperator(LogicalOperator $token): DqlPart
    {
        switch ($token->getOperator()) {
            case FilterQueryLexer::LOGICAL_OPERATOR_AND:
                return new DqlPart('AND');

            case FilterQueryLexer::LOGICAL_OPERATOR_OR:
                return new DqlPart('OR');
        }

        throw new \RuntimeException(sprintf('Unknown logical operator "%s"', $token->getOperator()));
    }

    private function buildOpeningParenthesis(): DqlPart
    {
        return new DqlPart('(');
    }

    private function loadLibraryFileAttributes()
    {
        if ($this->libraryFileAttributes === null) {
            $this->libraryFileAttributes= [];

            /** @var LibraryFileAttribute $a */
            foreach ($this->entityManager->getRepository(LibraryFileAttribute::class)->findAll() as $a) {
                $this->libraryFileAttributes[$a->getName()] = $a;
            }
        }
    }
}
