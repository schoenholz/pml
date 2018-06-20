<?php

namespace App\Tests\FilterQuery;

use App\Entity\LibraryFileAttribute;
use App\FilterQuery\DqlBuilder;
use App\FilterQuery\FilterQueryLexer;
use App\FilterQuery\Token\AttributeFilter;
use App\FilterQuery\Token\ClosingParenthesis;
use App\FilterQuery\Token\LogicalOperator;
use App\FilterQuery\Token\OpeningParenthesis;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DqlBuilderTest extends TestCase
{
    /**
     * @var LibraryFileAttribute[]
     */
    private $attributes = [];

    /**
     * @var DqlBuilder|MockObject
     */
    private $dqlBuilder;

    public function setUp()
    {
        $attributes = [
            'is_favourite' => LibraryFileAttribute::TYPE_BOOL,
            'release_date' => LibraryFileAttribute::TYPE_DATE,
            'release_time' => LibraryFileAttribute::TYPE_DATE_TIME,
            'bpm' => LibraryFileAttribute::TYPE_FLOAT,
            'year' => LibraryFileAttribute::TYPE_INT,
            'genre' => LibraryFileAttribute::TYPE_STRING,
        ];

        foreach ($attributes as $name => $type) {
            $this->attributes[$name] = new LibraryFileAttribute();
            $this->attributes[$name]
                ->setType($type)
                ->setName($name);
        }

        $this->dqlBuilder = $this
            ->getMockBuilder(DqlBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock()
        ;

        $this
            ->dqlBuilder
            ->method('getAttribute')
            ->willReturnCallback(function ($name) {
                return $this->attributes[$name];
            })
        ;
    }

    public function tearDown()
    {
        $this->attributes = [];
        $this->dqlBuilder = null;
    }

    public function testAttributeFilterEqSingleBool()
    {
        $token = new AttributeFilter('is_favourite', FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL, [true]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueBool = :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['is_favourite'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], true);
    }

    public function testAttributeFilterNeqSingleBool()
    {
        $token = new AttributeFilter('is_favourite', FilterQueryLexer::RELATIONAL_OPERATOR_NOT_EQUAL, [true]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('NOT EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueBool = :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['is_favourite'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], true);
    }

    public function testAttributeFilterEqSingleDate()
    {
        $token = new AttributeFilter('release_date', FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL, ['2018-01-01']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDate = :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_date'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '2018-01-01');
    }

    public function testAttributeFilterGtSingleDate()
    {
        $token = new AttributeFilter('release_date', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER, ['2018-01-01']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDate > :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_date'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '2018-01-01');
    }

    public function testAttributeFilterGteSingleDate()
    {
        $token = new AttributeFilter('release_date', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER_EQUAL, ['2018-01-01']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDate >= :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_date'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '2018-01-01');
    }

    public function testAttributeFilterLtSingleDate()
    {
        $token = new AttributeFilter('release_date', FilterQueryLexer::RELATIONAL_OPERATOR_LESS, ['2018-01-01']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDate < :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_date'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '2018-01-01');
    }

    public function testAttributeFilterLteSingleDate()
    {
        $token = new AttributeFilter('release_date', FilterQueryLexer::RELATIONAL_OPERATOR_LESS_EQUAL, ['2018-01-01']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDate <= :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_date'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '2018-01-01');
    }

    public function testAttributeFilterEqMultiDate()
    {
        $token = new AttributeFilter('release_date', FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL, ['2018-01-01', '2018-01-02']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDate IN(:value_1))', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_date'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], ['2018-01-01', '2018-01-02']);
    }

    public function testAttributeFilterEqSingleDateTime()
    {
        $token = new AttributeFilter('release_time', FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL, ['2018-01-01 00:00:01']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDateTime = :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_time'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '2018-01-01 00:00:01');
    }

    public function testAttributeFilterGtSingleDateTime()
    {
        $token = new AttributeFilter('release_time', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER, ['2018-01-01 00:00:01']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDateTime > :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_time'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '2018-01-01 00:00:01');
    }

    public function testAttributeFilterGteSingleDateTime()
    {
        $token = new AttributeFilter('release_time', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER_EQUAL, ['2018-01-01 00:00:01']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDateTime >= :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_time'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '2018-01-01 00:00:01');
    }

    public function testAttributeFilterLtSingleDateTime()
    {
        $token = new AttributeFilter('release_time', FilterQueryLexer::RELATIONAL_OPERATOR_LESS, ['2018-01-01 00:00:01']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDateTime < :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_time'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '2018-01-01 00:00:01');
    }

    public function testAttributeFilterLteSingleDateTime()
    {
        $token = new AttributeFilter('release_time', FilterQueryLexer::RELATIONAL_OPERATOR_LESS_EQUAL, ['2018-01-01 00:00:01']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDateTime <= :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_time'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '2018-01-01 00:00:01');
    }

    public function testAttributeFilterEqMultiDateTime()
    {
        $token = new AttributeFilter('release_time', FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL, ['2018-01-01 00:00:01', '2018-01-02 00:00:02']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueDateTime IN(:value_1))', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['release_time'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], ['2018-01-01 00:00:01', '2018-01-02 00:00:02']);
    }

    public function testAttributeFilterEqSingleFloat()
    {
        $token = new AttributeFilter('bpm', FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL, [100]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueFloat = :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['bpm'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 100);
    }

    public function testAttributeFilterGtSingleFloat()
    {
        $token = new AttributeFilter('bpm', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER, [100]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueFloat > :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['bpm'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 100);
    }

    public function testAttributeFilterGteSingleFloat()
    {
        $token = new AttributeFilter('bpm', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER_EQUAL, [100]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueFloat >= :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['bpm'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 100);
    }

    public function testAttributeFilterLtSingleFloat()
    {
        $token = new AttributeFilter('bpm', FilterQueryLexer::RELATIONAL_OPERATOR_LESS, [100]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueFloat < :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['bpm'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 100);
    }

    public function testAttributeFilterLteSingleFloat()
    {
        $token = new AttributeFilter('bpm', FilterQueryLexer::RELATIONAL_OPERATOR_LESS_EQUAL, [100]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueFloat <= :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['bpm'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 100);
    }

    public function testAttributeFilterEqMultiFloat()
    {
        $token = new AttributeFilter('bpm', FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL, [100, '100-01-02']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueFloat IN(:value_1))', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['bpm'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], [100, '100-01-02']);
    }

    public function testAttributeFilterEqSingleInt()
    {
        $token = new AttributeFilter('year', FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL, [2018]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueInt = :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['year'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 2018);
    }

    public function testAttributeFilterGtSingleInt()
    {
        $token = new AttributeFilter('year', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER, [2018]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueInt > :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['year'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 2018);
    }

    public function testAttributeFilterGteSingleInt()
    {
        $token = new AttributeFilter('year', FilterQueryLexer::RELATIONAL_OPERATOR_GREATER_EQUAL, [2018]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueInt >= :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['year'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 2018);
    }

    public function testAttributeFilterLtSingleInt()
    {
        $token = new AttributeFilter('year', FilterQueryLexer::RELATIONAL_OPERATOR_LESS, [2018]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueInt < :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['year'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 2018);
    }

    public function testAttributeFilterLteSingleInt()
    {
        $token = new AttributeFilter('year', FilterQueryLexer::RELATIONAL_OPERATOR_LESS_EQUAL, [2018]);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueInt <= :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['year'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 2018);
    }

    public function testAttributeFilterEqMultiInt()
    {
        $token = new AttributeFilter('year', FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL, [2018, '2018-01-02']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueInt IN(:value_1))', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['year'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], [2018, '2018-01-02']);
    }

    public function testAttributeFilterEqSingleStr()
    {
        $token = new AttributeFilter('genre', FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL, ['Rock']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueString = :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['genre'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 'Rock');
    }

    public function testAttributeFilterEqMultiStr()
    {
        $token = new AttributeFilter('genre', FilterQueryLexer::RELATIONAL_OPERATOR_EQUAL, ['Rock', 'Heavy Metal']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueString IN(:value_1))', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['genre'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], ['Rock', 'Heavy Metal']);
    }

    public function testAttributeFilterNeqSingleStr()
    {
        $token = new AttributeFilter('genre', FilterQueryLexer::RELATIONAL_OPERATOR_NOT_EQUAL, ['Rock']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('NOT EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueString = :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['genre'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], 'Rock');
    }

    public function testAttributeFilterNeqMultiStr()
    {
        $token = new AttributeFilter('genre', FilterQueryLexer::RELATIONAL_OPERATOR_NOT_EQUAL, ['Rock', 'Heavy Metal']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('NOT EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueString IN(:value_1))', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['genre'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], ['Rock', 'Heavy Metal']);
    }

    public function testAttributeFilterContainsSingleStr()
    {
        $token = new AttributeFilter('genre', FilterQueryLexer::RELATIONAL_OPERATOR_CONTAINS, ['Rock']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueString LIKE :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['genre'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '%Rock%');
    }

    public function testAttributeFilterContainsMultiStr()
    {
        $token = new AttributeFilter('genre', FilterQueryLexer::RELATIONAL_OPERATOR_CONTAINS, ['Rock', 'Heavy Metal']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND (lfav_1.valueString LIKE :value_1 OR lfav_1.valueString LIKE :value_2))', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(3, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['genre'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '%Rock%');
        $this->assertArrayHasKey('value_2', $params);
        $this->assertEquals($params['value_2'], '%Heavy Metal%');
    }

    public function testAttributeFilterNotContainsSingleStr()
    {
        $token = new AttributeFilter('genre', FilterQueryLexer::RELATIONAL_OPERATOR_NOT_CONTAINS, ['Rock']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('NOT EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND lfav_1.valueString LIKE :value_1)', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(2, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['genre'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '%Rock%');
    }

    public function testAttributeFilterNotContainsMultiStr()
    {
        $token = new AttributeFilter('genre', FilterQueryLexer::RELATIONAL_OPERATOR_NOT_CONTAINS, ['Rock', 'Heavy Metal']);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('NOT EXISTS(SELECT 1 FROM App\Entity\LibraryFileAttributeValue lfav_1 WHERE lfav_1.libraryFile = lf.id AND lfav_1.libraryFileAttribute = :attribute_1 AND (lfav_1.valueString LIKE :value_1 OR lfav_1.valueString LIKE :value_2))', $res->getDql());

        $params = $res->getParams();
        $this->assertCount(3, $params);
        $this->assertArrayHasKey('attribute_1', $params);
        $this->assertSame($this->attributes['genre'], $params['attribute_1']);
        $this->assertArrayHasKey('value_1', $params);
        $this->assertEquals($params['value_1'], '%Rock%');
        $this->assertArrayHasKey('value_2', $params);
        $this->assertEquals($params['value_2'], '%Heavy Metal%');
    }

    public function testClosingParenthesis()
    {
        $token = new ClosingParenthesis();
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals(')', $res->getDql());
        $this->assertCount(0, $res->getParams());
    }

    public function testLogicalOperatorAnd()
    {
        $token = new LogicalOperator(FilterQueryLexer::LOGICAL_OPERATOR_AND);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('AND', $res->getDql());
        $this->assertCount(0, $res->getParams());
    }

    public function testLogicalOperatorOr()
    {
        $token = new LogicalOperator(FilterQueryLexer::LOGICAL_OPERATOR_OR);
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('OR', $res->getDql());
        $this->assertCount(0, $res->getParams());
    }

    public function testOpeningParenthesis()
    {
        $token = new OpeningParenthesis();
        $res = $this->dqlBuilder->build($token);

        $this->assertEquals('(', $res->getDql());
        $this->assertCount(0, $res->getParams());
    }
}
