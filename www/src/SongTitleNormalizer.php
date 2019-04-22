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
        'album version explicit',
        'album version',
        'album version & edit',
        'clean',
        'dj mix',
        'dj version',
        'edit version',
        'edit',
        'explicit version',
        'explicit',
        'extended mix',
        'extended version',
        'extended',
        'free track',
        'free',
        'full length dj mix',
        'full length dj version',
        'full',
        'live',
        'mastered',
        'mix cut',
        'orig',
        'original edit',
        'original mix edit',
        'original mix',
        'original version',
        'original',
        'pro mix',
        'radio edit',
        'radio single version',
        'radio version',
        're-mastered',
        'remastered',
        'streaming version',
        'streaming edit',
        'release edit',
        'live edit',
        'short edit',
        'clean edit',
        'single edit',
        'dj edit',
        'explicit edit',
        'qore 3.0 - ost',
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

        foreach ([
            '/\s*\(feat\. [^)]+\)/' => '', // Strip "feat."
            '/\s*\(ft\. [^)]+\)/' => '', // Strip "ft."
            '/\s+(\))/' => '\\1', // Remove whitespace before ")"
            '/(\()\s+/' => '\\1', // Remove whitespace after "("
            '/\s*\(abgt\d+\)/' => '', // Remove "ABGT ..." addition
            '/(?<=[^\(]\s)(?:extended|radio) (mix|edit\))/' => '\\1', // (bar extended|radio mix|edit) -> (bar mix)
            '/(?<=[^\(]\s)extended (club mix\))/' => '\\1', // (bar extended club mix) -> (bar club mix)
            '/(\()(?:extended )([\w\s]+ mix\))/' => '\\1\\2', // (extended club mix) -> (club mix)
            '/(\()live at [\w\s]+\)/' => '', // Strip "(live at foo bar)"
        ] as $pattern => $replacement) {
            $t = preg_replace($pattern, $replacement, $t);
        }

        // Additions
        foreach ($this->additions as $addition) {
            $pattern = '/\(' . preg_quote($addition, '/') . '\)/';
            $t = preg_replace($pattern, '', $t);

            // Strip "(... - edit|pro mix)"
            $pattern = '/(\([^(]+) - (?:' . preg_quote($addition, '/') . ')(\))/';
            $t = preg_replace($pattern, '\\1\\2', $t);
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
