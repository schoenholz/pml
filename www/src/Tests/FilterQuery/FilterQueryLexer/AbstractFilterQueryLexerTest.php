<?php

namespace App\Tests\FilterQuery\FilterQueryLexer;

use App\Entity\LibraryFileAttribute;
use App\FilterQuery\FilterQueryLexer;
use App\FilterQuery\ValueCaster;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractFilterQueryLexerTest extends TestCase
{
    /**
     * @var FilterQueryLexer|MockObject
     */
    protected $lexer;

    /**
     * @var LibraryFileAttribute[]
     */
    protected $attributes = [];

    public function setUp()
    {
        $attributes = [
            'is_favourite' => LibraryFileAttribute::TYPE_BOOL,
            'release_date' => LibraryFileAttribute::TYPE_DATE,
            'release_time' => LibraryFileAttribute::TYPE_DATE_TIME,
            'bpm' => LibraryFileAttribute::TYPE_FLOAT,
            'genre' => LibraryFileAttribute::TYPE_STRING,
            'year' => LibraryFileAttribute::TYPE_INT,
        ];

        foreach ($attributes as $name => $type) {
            $this->attributes[$name] = new LibraryFileAttribute();
            $this->attributes[$name]
                ->setType($type)
                ->setName($name)
            ;
        }

        $this->lexer = $this
            ->getMockBuilder(FilterQueryLexer::class)
            ->setConstructorArgs([$this->createMock(EntityManager::class), new ValueCaster()])
            ->setMethods(['getAttribute'])
            ->getMock()
        ;

        $this
            ->lexer
            ->method('getAttribute')
            ->willReturnCallback(function ($name) {
                return $this->attributes[$name];
            })
        ;
    }

    public function tearDown()
    {
        $this->lexer = null;
        $this->attributes = [];
    }
}
