<?php

namespace App;

class SongTitleNormalizer
{
    /**
     * @var \Transliterator
     */
    private $transliterator;

    private $additions = [
        'edit',
        'extended mix',
        'extended version',
        'extended',
        'full length dj mix',
        'full length dj version',
        'mix cut',
        'original edit',
        'original mix edit',
        'original mix',
        'original version',
        'original',
        'pro mix',
        'radio edit',
        'radio version',
    ];

    public function normalize(string $title): string
    {
        if ($this->transliterator === null) {
            $this->transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');;
        }

        $t = mb_strtolower($this->transliterator->transliterate($title));

        foreach ($this->additions as $addition) {
            $pattern = '/\(' . preg_quote($addition, '/') . '\)/';
            $t = preg_replace($pattern, '', $t);

            $pattern = '/\[' . preg_quote($addition, '/') . '\]/';
            $t = preg_replace($pattern, '', $t);
        }

        return $t;
    }
}
