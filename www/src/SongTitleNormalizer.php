<?php

namespace App;

class SongTitleNormalizer
{
    /**
     * @var \Transliterator
     */
    private $transliterator;

    private $additions = [
        'album edit',
        'album mix',
        'album version & edit',
        'album version explicit',
        'album version',
        'clean edit',
        'clean',
        'dj edit',
        'dj mix',
        'dj version',
        'edit version',
        'edit',
        'explicit edit',
        'explicit version',
        'explicit',
        'extended mix',
        'extended version',
        'extended',
        'free track',
        'free release',
        'free',
        'full length dj mix',
        'full length dj version',
        'full',
        'live edit',
        'live',
        'mastered',
        'mix cut',
        'mixed',
        'orig',
        'original edit',
        'original mix edit',
        'original mix',
        'original version',
        'original',
        'pro mix',
        'qore 3.0 - ost',
        'radio edit',
        'radio single version',
        'radio version',
        're-mastered',
        'release edit',
        'remastered',
        'short edit',
        'single edit',
        'streaming edit',
        'streaming version',
    ];

    private $suffixes = [
        '#tih',
    ];

    public function normalize(string $title): string
    {
        if ($this->transliterator === null) {
            $this->transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');;
        }

        $t = mb_strtolower($this->transliterator->transliterate($title));
        $t = str_replace(['"', '´', '`', "'"], "", $t);
        //$t = str_replace([' & '], " and ", $t);
        $t = strtr($t, [
            '[' => '(',
            ']' => ')',
            '{' => '(',
            '}' => ')',
            'remix edit)' => 'remix)',
        ]);

        // Additions
        foreach ($this->additions as $addition) {
            $pattern = '/\(' . preg_quote($addition, '/') . '\)/';
            $t = preg_replace($pattern, '', $t);

            // Strip "(... - edit|pro mix)"
            $pattern = '/(\([^(]+)\s*- (?:' . preg_quote($addition, '/') . ')(\))/';
            $t = preg_replace($pattern, '\\1\\2', $t);
        }

        foreach ([
            '/\s*\(feat\. [^)]+\)/' => '', // Strip "feat."
            '/\s*\(ft\. [^)]+\)/' => '', // Strip "ft."
            '/\s+(\))/' => '\\1', // Remove whitespace before ")"
            '/(\()\s+/' => '\\1', // Remove whitespace after "("
            '/\s*\(abgt\d+\)/' => '', // Remove "ABGT ..." addition
            '/(?<=[^\(]\s)(?:extended|radio) (remix|mix|edit\))/' => '\\1', // (bar extended|radio mix|edit) -> (bar mix)
            '/(?<=[^\(]\s)extended (club mix\))/' => '\\1', // (bar extended club mix) -> (bar club mix)
            '/(\()(?:extended )([\w\s]+ mix\))/' => '\\1\\2', // (extended club mix) -> (club mix)
            '/(\()live at [\w\s]+\)/' => '', // Strip "(live at foo bar)"
            '/\s*\/\s*/' => '/', // " / " -> "/"
            '/\s*:\s*/' => ': ', // ":" -> ": "
            '/(\(.+ )rmx(\))/' => '\\1 remix\\2', // "(... rmx)" -> "(... remix)"
            '/(\(.+ mix) edit(\))/' => '\\1\\2', // "(... mix edit) -> "(... mix)"
        ] as $pattern => $replacement) {
            $t = preg_replace($pattern, $replacement, $t);
        }

        // Suffixes
        foreach ($this->suffixes as $suffix) {
            $pattern = '/(?<=\s)' . preg_quote($suffix, '/') . '\s*(?=$|\s*\([^\(\)]+\)$)/i';
            $t = preg_replace($pattern, '', $t);
        }

        // Anthem
        $t = preg_replace('/(?=\S+\s)\(\S.* (?:(?:\d{4} anthem)|(?:anthem \d{4}))\)/', '', $t);
        $t = preg_replace('/\(official \w+ anthem\)/', '', $t);

        $t = preg_replace('/\s+/', ' ', trim($t));

        return $t;
    }
}
