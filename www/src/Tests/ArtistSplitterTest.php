<?php

namespace App\Tests;

use App\ArtistSplitter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ArtistSplitterTest extends TestCase
{
    /**
     * @var MockObject|ArtistSplitter
     */
    private $artistSplitter;

    public function setUp()
    {
        $this->artistSplitter = $this
            ->getMockBuilder(ArtistSplitter::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getProtectedArtists', // Return composition
            ])
            ->getMock()
        ;

        $this->artistSplitter
            ->method('getProtectedArtists')
            ->willReturn([
                'Artist 1 & Artist 2',
            ])
        ;
    }

    public function tearDown()
    {
        $this->artistSplitter = null;
    }

    /**
     * @dataProvider getDelimiters
     */
    public function testArtistsAreSplit(string $delimiter)
    {
        $str = 'Artist A' . $delimiter . 'Artist B';
        $split = $this->artistSplitter->split($str);
        $this->assertEquals([
            'Artist A',
            'Artist B',
        ], $split);
    }

    public function testProtectedArtistsAreNotSplit()
    {
        $str = 'Artist 1 & Artist 2';
        $split = $this->artistSplitter->split($str);
        $this->assertEquals(['Artist 1 & Artist 2'], $split);
    }

    public function testProtectedArtistsAreNotSplitButRemaindersAre()
    {
        $str = 'Artist A & Artist 1 & Artist 2 & Artist B';
        $split = $this->artistSplitter->split($str);
        sort($split);

        $expected = [
            'Artist A',
            'Artist 1 & Artist 2',
            'Artist B',
        ];
        sort($expected);

        $this->assertEquals($expected, $split);
    }

    public function testProtectedArtistsAreNotSplitButPrefixIs()
    {
        $str = 'Artist A & Artist B & Artist 1 & Artist 2';
        $split = $this->artistSplitter->split($str);
        sort($split);

        $expected = [
            'Artist A',
            'Artist B',
            'Artist 1 & Artist 2',
        ];
        sort($expected);

        $this->assertEquals($expected, $split);
    }

    public function testProtectedArtistsAreNotSplitButSuffixIs()
    {
        $str = 'Artist 1 & Artist 2 & Artist A & Artist B';
        $split = $this->artistSplitter->split($str);
        sort($split);

        $expected = [
            'Artist 1 & Artist 2',
            'Artist A',
            'Artist B',
        ];
        sort($expected);

        $this->assertEquals($expected, $split);
    }

    public function getDelimiters(): array
    {
        return [
            [' & '],
            [','],
            [', '],
            [';'],
            ['; '],
            [' , '],
            [' ,'],
            [' feat. '],
            [' Feat. '],
            [' FEAT. '],
            [' vs. '],
            [' Vs. '],
            [' VS. '],
        ];
    }

    public function getFoo(): array
    {
        return [
            [
                '',
                '',
                ' & ',
            ],
        ];
    }
}
