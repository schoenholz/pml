<?php

namespace App\Tests\FilterQuery;

use App\Entity\LibraryFileAttribute;
use App\Exception\FilterQuerySemanticException;
use App\FilterQuery\ValueCaster;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ValueCasterTest extends TestCase
{
    /**
     * @var ValueCaster
     */
    private $valueCaster;

    public function setUp()
    {
        $this->valueCaster = new ValueCaster();
    }

    public function tearDown()
    {
        $this->valueCaster = null;
    }

    public function getAttributeTypes(): array
    {
        return array_map(function ($type) {
            return [$type];
        }, LibraryFileAttribute::TYPES);
    }

    public function getInvalidBoolValues(): array
    {
        return [
            [2],
            ['foo'],
        ];
    }

    public function getInvalidIntValues(): array
    {
        return [
            [1.1],
            ['1.1'],
            ['foo'],
            ['123.456.789'],
        ];
    }

    public function getInvalidFloatValues(): array
    {
        return [
            ['foo'],
            ['123.456.789'],
        ];
    }

    public function getInvalidDateValues(): array
    {
        return [
            [1],
            [12],
            [123],
            [12345],
            ['1'],
            ['12'],
            ['123'],
            ['12345'],
            ['foo'],
            ['2018-003-01'],
            [' 2018-03-01'],
            ['2018-03-01 '],
            ['2018-03-011'],
            ['2018.03.11'],
            ['11.03.2018'],
            ['2018-03-01 00:00:00'],
        ];
    }

    public function getInvalidDateTimeValues(): array
    {
        return [
            [1],
            [12],
            [123],
            [12345],
            ['1'],
            ['12'],
            ['123'],
            ['12345'],
            ['foo'],
            ['2018-003-01'],
            [' 2018-03-01'],
            ['2018-03-01 '],
            ['2018-03-011'],
            ['2018.03.11'],
            ['11.03.2018'],
            ['2018-03-01 1'],
            ['2018-03-01 0.0.0'],
        ];
    }

    public function testNoAttributeTypeRaisesException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('%s has no type', LibraryFileAttribute::class));

        $attribute = $this->getAttributeMock();
        $attribute
            ->method('getType')
            ->willReturn(null)
        ;

        $this->valueCaster->cast($attribute, 'foo');
    }

    public function testUnknownAttributeTypeRaisesException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('%s has invalid type "bar"', LibraryFileAttribute::class));

        $attribute = $this->getAttributeMock();
        $attribute
            ->method('getType')
            ->willReturn('bar')
        ;

        $this->valueCaster->cast($attribute, 'foo');
    }

    /**
     * @dataProvider getAttributeTypes
     *
     * @param string $type
     */
    public function testNullValueRaisesException(string $type)
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage(sprintf('Value of type NULL is invalid for attribute "foo" of type "%s"', $type));

        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType($type)
            ->setName('foo')
        ;

        $this->valueCaster->cast($attribute, null);
    }

    /**
     * @dataProvider getAttributeTypes
     *
     * @param string $type
     */
    public function testStdObjectValueRaisesException(string $type)
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage(sprintf('Value of type object is invalid for attribute "foo" of type "%s"', $type));

        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType($type)
            ->setName('foo')
        ;

        $this->valueCaster->cast($attribute, new \stdClass());
    }

    /**
     * @dataProvider getInvalidBoolValues
     *
     * @param mixed $v
     */
    public function testInvalidBoolValueRaisesException($v)
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage(sprintf('Value (%s) "%s" is invalid for attribute "foo" of type "%s"', gettype($v), $v, LibraryFileAttribute::TYPE_BOOL));

        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_BOOL)
            ->setName('foo')
        ;

        $this->valueCaster->cast($attribute, $v);
    }

    /**
     * @dataProvider getInvalidIntValues
     *
     * @param mixed $v
     */
    public function testInvalidIntValueRaisesException($v)
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage(sprintf('Value (%s) "%s" is invalid for attribute "foo" of type "%s"', gettype($v), $v, LibraryFileAttribute::TYPE_INT));

        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_INT)
            ->setName('foo')
        ;

        $this->valueCaster->cast($attribute, $v);
    }

    /**
     * @dataProvider getInvalidFloatValues
     *
     * @param mixed $v
     */
    public function testInvalidFloatValueRaisesException($v)
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage(sprintf('Value (%s) "%s" is invalid for attribute "foo" of type "%s"', gettype($v), $v, LibraryFileAttribute::TYPE_FLOAT));

        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_FLOAT)
            ->setName('foo')
        ;

        $this->valueCaster->cast($attribute, $v);
    }

    /**
     * @dataProvider getInvalidDateValues
     *
     * @param mixed $v
     */
    public function testInvalidDateValueRaisesException($v)
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage(sprintf('Value (%s) "%s" is invalid for attribute "foo" of type "%s"', gettype($v), $v, LibraryFileAttribute::TYPE_DATE));

        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE)
            ->setName('foo')
        ;

        $this->valueCaster->cast($attribute, $v);
    }

    public function testIntIsValid()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_INT)
            ->setName('foo')
        ;

        $this->assertSame(1, $this->valueCaster->cast($attribute, 1));
    }

    public function testStringIsConvertedToInt()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_INT)
            ->setName('foo')
        ;

        $this->assertSame(1, $this->valueCaster->cast($attribute, '1'));
    }

    public function testYearIsConvertedToDate()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE)
            ->setName('foo')
        ;

        $this->assertSame('2018-01-01', $this->valueCaster->cast($attribute, 2018));
    }

    public function testYearAndMonthIsConvertedToDate()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE)
            ->setName('foo')
        ;

        $this->assertSame('2018-03-01', $this->valueCaster->cast($attribute, '2018-03'));
    }

    public function testDateIsConverted()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE)
            ->setName('foo')
        ;

        $this->assertSame('2018-03-04', $this->valueCaster->cast($attribute, '2018-3-4'));
    }

    public function testDateIsValid()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE)
            ->setName('foo')
        ;

        $this->assertSame('2018-03-04', $this->valueCaster->cast($attribute, '2018-03-04'));
    }

    /**
     * @dataProvider getInvalidDateTimeValues
     *
     * @param mixed $v
     */
    public function testInvalidDateTimeValueRaisesException($v)
    {
        $this->expectException(FilterQuerySemanticException::class);
        $this->expectExceptionMessage(sprintf('Value (%s) "%s" is invalid for attribute "foo" of type "%s"', gettype($v), $v, LibraryFileAttribute::TYPE_DATE_TIME));

        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE_TIME)
            ->setName('foo')
        ;

        $this->valueCaster->cast($attribute, $v);
    }

    public function testDateTimeIsValid()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE_TIME)
            ->setName('foo')
        ;

        $this->assertSame('2018-03-04 13:04:05', $this->valueCaster->cast($attribute, '2018-03-04 13:04:05'));
    }

    public function testDateTimeIsConverted()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE_TIME)
            ->setName('foo')
        ;

        $this->assertSame('2018-03-04 13:04:05', $this->valueCaster->cast($attribute, '2018-3-4 13:4:5'));
    }

    public function testDateWithHourAndMinuteIsConvertedToDateTime()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE_TIME)
            ->setName('foo')
        ;

        $this->assertSame('2018-03-04 13:04:00', $this->valueCaster->cast($attribute, '2018-3-4 13:4'));
    }

    public function testYearIsConvertedToDateTime()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE_TIME)
            ->setName('foo')
        ;

        $this->assertSame('2018-01-01 00:00:00', $this->valueCaster->cast($attribute, 2018));
    }

    public function testYearAndMonthIsConvertedToDateTime()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE_TIME)
            ->setName('foo')
        ;

        $this->assertSame('2018-03-01 00:00:00', $this->valueCaster->cast($attribute, '2018-03'));
    }

    public function testDateIsConvertedToDateTime()
    {
        $attribute = new LibraryFileAttribute();
        $attribute
            ->setType(LibraryFileAttribute::TYPE_DATE_TIME)
            ->setName('foo')
        ;

        $this->assertSame('2018-03-04 00:00:00', $this->valueCaster->cast($attribute, '2018-03-04'));
    }

    /**
     * @return MockObject|LibraryFileAttribute
     */
    private function getAttributeMock()
    {
        return $this
            ->getMockBuilder(LibraryFileAttribute::class)
            ->setMethods(['getType'])
            ->getMock()
        ;
    }
}
