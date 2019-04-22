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

        $this->assertArrayContainsOnly([
            'Artist A',
            'Artist B',
        ], $split);
    }

    public function testProtectedArtistsAreNotSplit()
    {
        $str = 'Artist 1 & Artist 2';
        $split = $this->artistSplitter->split($str);

        $this->assertArrayContainsOnly([
            'Artist 1 & Artist 2',
        ], $split);
    }

    public function testProtectedArtistsAreNotSplitButRemaindersAre()
    {
        $str = 'Artist A & Artist 1 & Artist 2 & Artist B';
        $split = $this->artistSplitter->split($str);

        $this->assertArrayContainsOnly([
            'Artist A',
            'Artist 1 & Artist 2',
            'Artist B',
        ], $split);
    }

    public function testProtectedArtistsAreNotSplitButPrefixIs()
    {
        $str = 'Artist A & Artist B & Artist 1 & Artist 2';
        $split = $this->artistSplitter->split($str);

        $this->assertArrayContainsOnly([
            'Artist A',
            'Artist B',
            'Artist 1 & Artist 2',
        ], $split);
    }

    public function testProtectedArtistsAreNotSplitButSuffixIs()
    {
        $str = 'Artist 1 & Artist 2 & Artist A & Artist B';
        $split = $this->artistSplitter->split($str);

        $this->assertArrayContainsOnly([
            'Artist 1 & Artist 2',
            'Artist A',
            'Artist B',
        ], $split);
    }

    /**
     * @dataProvider getTrackTitleArtists
     *
     * @param string $trackArtist
     * @param array $expectedArtists
     */
    public function testArtistIsExtractedFromTrackTitle(string $trackArtist, array $expectedArtists)
    {
        $str = 'Foobar (' . $trackArtist . ')';
        $split = $this->artistSplitter->split('Artist A', $str);

        $this->assertArrayContainsOnly(array_merge(['Artist A'], $expectedArtists), $split);
    }

    public function testDuplicateArtistExtractedFromTrackTitleIsReturnedOnce()
    {
        $str = 'Foobar (feat. Artist B)';
        $split = $this->artistSplitter->split('Artist A & Artist B', $str);

        $this->assertArrayContainsOnly([
            'Artist A',
            'Artist B',
        ], $split);
    }

    public function testTrackAdditionIsNotUsedAsAdditionalArtist()
    {
        $str = 'Foobar (feat. Artist B) (Bartar)';
        $split = $this->artistSplitter->split('Artist A', $str);

        $this->assertArrayContainsOnly([
            'Artist A',
            'Artist B',
        ], $split);
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
            [' featuring '],
            [' feat. '],
            [' Feat. '],
            [' FEAT. '],
            [' feat '],
            [' FEAT '],
            [' vs. '],
            [' Vs. '],
            [' VS. '],
            [' x '],
            [' pres. '],
            [' presents '],
            [' and '], // Experimental
        ];
    }

    public function getTrackTitleArtists(): array
    {
        return [
            [
                'feat. Artist B',
                ['Artist B'],
            ],
            [
                'feat.  Artist B',
                ['Artist B'],
            ],
            [
                ' feat. Artist B',
                ['Artist B'],
            ],
            [
                'feat. Artist B ',
                ['Artist B'],
            ],
            [
                ' feat. Artist B ',
                ['Artist B'],
            ],
            [
                'Feat. Artist B',
                ['Artist B'],
            ],
            [
                'FEAT. Artist B',
                ['Artist B'],
            ],
            [
                'feat Artist B',
                ['Artist B'],
            ],
            [
                'ft. Artist B',
                ['Artist B'],
            ],
            [
                'featuring Artist B',
                ['Artist B'],
            ],
            [
                'feat. Artist B & Artist C',
                ['Artist B', 'Artist C'],
            ],
        ];
    }

    private function assertArrayContainsOnly(array $expected, array $actual)
    {
        sort($expected);
        $e = array_values($expected);

        sort($actual);
        $a = array_values($actual);

        $this->assertEquals($e, $a);
    }
}
