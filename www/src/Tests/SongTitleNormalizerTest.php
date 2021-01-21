<?php

namespace App\Tests;

use App\SongTitleNormalizer;
use PHPUnit\Framework\TestCase;

class SongTitleNormalizerTest extends TestCase
{
    /**
     * @dataProvider getTitles
     *
     * @param string $source
     * @param string $target
     */
    public function testTitleIsNormalized(string $source, string $target)
    {
        $normalizer = new SongTitleNormalizer();

        $this->assertEquals($target, $normalizer->normalize($source));
    }

    public function getTitles(): array
    {
        return [
            ['foo', 'foo'],
            ['foo (original mix)', 'foo'],
            ['foo (mix cut)', 'foo'],
            ['foo (original)', 'foo'],
            ['foo (edit)', 'foo'],
            ['foo (explicit version)', 'foo'],
            ['foo (explicit)', 'foo'],
            ['foo (live)', 'foo'],
            ['foo (orig)', 'foo'],
            ['foo (full)', 'foo'],
            ['foo (clean)', 'foo'],
            ['foo (streaming version)', 'foo'],
            ['foo (album edit)', 'foo'],
            ['foo (album version & edit)', 'foo'],
            ['foo (dj mix)', 'foo'],
            ['foo (dj version)', 'foo'],
            ['foo (free)', 'foo'],
            ['foo (free release)', 'foo'],
            ['foo (free track)', 'foo'],
            ['foo (radio single version)', 'foo'],
            ['foo (release edit)', 'foo'],
            ['foo (explicit edit)', 'foo'],
            ['foo (dj edit)', 'foo'],
            ['foo (single edit)', 'foo'],
            ['foo (clean edit)', 'foo'],
            ['foo (short edit)', 'foo'],
            ['foo (live edit)', 'foo'],
            ['foo (qore 3.0 - ost)', 'foo'],
            ['foo (mixed)', 'foo'],

            // Lowercase
            ['Foo', 'foo'],

            // Transliterate
            ['Föö', 'foo'],

            // Quotes
            ['foo\'bar', 'foobar'],
            ['foo"bar', 'foobar'],
            ['foo`bar', 'foobar'],
            ['foo´bar', 'foobar'],
            ['foo’bar', 'foobar'],

            // Whitespace
            ['foo  bar', 'foo bar'],
            ['foo ', 'foo'],
            [' foo', 'foo'],
            ['foo (bar )', 'foo (bar)'],
            ['foo ( bar)', 'foo (bar)'],
            ['foo ( bar )', 'foo (bar)'],

            // Parenthesis
            ['foo (bar)', 'foo (bar)'],
            ['foo [bar]', 'foo (bar)'],
            ['foo {bar}', 'foo (bar)'],

            // "Remix edit"
            ['foo (bar remix edit)', 'foo (bar remix)'],
            ['foo (bar remix edit baz)', 'foo (bar remix edit baz)'],

            // "Feat."
            ['foo (feat. bar)', 'foo'],
            ['foo (ft. bar)', 'foo'],
            ['foo (feat. bar, baz)', 'foo'],
            ['foo (ft. bar, baz)', 'foo'],
            ['foo (feat. bar) (baz)', 'foo (baz)'],
            ['foo (ft. bar) (baz)', 'foo (baz)'],
            ['foo (baz) (feat. bar)', 'foo (baz)'],
            ['foo (baz) (ft. bar)', 'foo (baz)'],
            ['foo (feat. bar) baz', 'foo baz'],
            ['foo (ft. bar) baz', 'foo baz'],

            // "ABGT"
            ['foo (ABGT123)', 'foo'],

            // TIH
            ['foo #TIH', 'foo'],
            ['foo #TIH (original mix)', 'foo'],
            ['foo #TIH(original mix)', 'foo'],
            ['foo #TIH (bar remix)', 'foo (bar remix)'],
            ['foo #TIH(bar remix)', 'foo (bar remix)'],
            ['foo #TIH  (bar remix)', 'foo (bar remix)'],
            ['foo (original mix) #TIH', 'foo'],
            ['foo #TIH foo', 'foo #tih foo'],

            //['foo - a state of trance 600 anthem', 'foo'],
            ['foo - a state of trance 600 anthem foo', 'foo - a state of trance 600 anthem foo'],

            // Anthem
            ['foo (bar 2018 Anthem)', 'foo'],
            ['foo (bar baz 2018 Anthem)', 'foo'],
            ['foo (bar Anthem 2018)', 'foo'],
            ['foo (bar baz Anthem 2018)', 'foo'],
            ['foo (Official foobar Anthem)', 'foo'],


            ['foo (extended club mix)', 'foo (club mix)'],
            ['foo [extended club mix]', 'foo (club mix)'],
            ['foo (bar extended club mix)', 'foo (bar club mix)'],
            ['foo (mastered)', 'foo'],
            ['foo (remastered)', 'foo'],
            ['foo (re-mastered)', 'foo'],
            ['foo (bar extended mix)', 'foo (bar mix)'],
            ['foo (bar radio mix)', 'foo (bar mix)'],
            ['foo (bar extended edit)', 'foo (bar edit)'],
            ['foo (bar extended remix)', 'foo (bar remix)'],
            ['foo (bar radio edit)', 'foo (bar edit)'],
            ['foo (w&w radio edit)', 'foo (w&w edit)'],
            ['foo (bar remix edit)', 'foo (bar remix)'],
            ['foo (bar mix edit)', 'foo (bar mix)'],
            ['foo (album version explicit)', 'foo'],
            ['foo (album version)', 'foo'],
            ['foo (live at bar baz)', 'foo'],
            ['foo (bar baz mix - edit)', 'foo (bar baz mix)'],
            ['foo (bar baz mix - pro mix)', 'foo (bar baz mix)'],
            ['foo (bar baz remix - edit)', 'foo (bar baz remix)'],
            ['foo (bar baz remix - pro mix)', 'foo (bar baz remix)'],
            ['foo (bar baz remix - album version)', 'foo (bar baz remix)'],
            ['foo (bar baz remix - album edit)', 'foo (bar baz remix)'],
            ['foo (bar baz remix - album mix)', 'foo (bar baz remix)'],
            ['foo (bar baz remix - explicit)', 'foo (bar baz remix)'],
            ['foo (bar baz rmx - explicit)', 'foo (bar baz remix)'],
            ['foo (bar baz mix- edit)', 'foo (bar baz mix)'],
            ['foo (bar baz mix- pro mix)', 'foo (bar baz mix)'],
            ['foo (bar baz remix- edit)', 'foo (bar baz remix)'],
            ['foo (bar baz remix- pro mix)', 'foo (bar baz remix)'],
            ['foo (bar baz remix- album version)', 'foo (bar baz remix)'],
            ['foo (bar baz remix- album edit)', 'foo (bar baz remix)'],
            ['foo (bar baz remix- album mix)', 'foo (bar baz remix)'],
            ['foo / bar', 'foo/bar'],
            ['foo /bar', 'foo/bar'],
            ['foo/ bar', 'foo/bar'],
            ['foo:bar', 'foo: bar'],
            ['foo…', 'foo...'],
            ['foo (bar rmx)', 'foo (bar remix)'],
            ['foo (barrmx)', 'foo (barrmx)'],
            ['foo (bar-rmx)', 'foo (bar-rmx)'],
            ['foo (bar rmxtar)', 'foo (bar rmxtar)'],

            //['foo (bonus track)', 'foo'],
            //['foo (classic bonus track)', 'foo'],
            //['foo (bonus)', 'foo'],
            //['foo (bonus album cut)', 'foo'],
            //['foo & bar', 'foo and bar'], // todo experimental
        ];
    }
}
