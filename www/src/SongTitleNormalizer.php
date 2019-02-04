<?php

namespace App;

class SongTitleNormalizer
{
    /**
     * @var \Transliterator
     */
    private $transliterator;

    private $additions = [
        'extended mix',
        'extended version',
        'extended',
        'mix cut',
        'original edit',
        'original mix',
        'original version',
        'original',
        'pro mix',
        'radio edit',
        'radio version',
    ];

    public function normalize(string $title)
    {
        if ($this->transliterator === null) {
            $this->transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');;
        }

        $t = mb_strtolower($this->transliterator->transliterate($title));

        foreach ($this->additions as $addition) {
            $pattern = '/\(' . preg_quote($addition, '/') . '\)/';
            $t = preg_replace($pattern, '', $t);
        }

        //preg_match('/\(.+\)/', $t, $matches);
        //if ($matches) {
        //    print_r($matches);
        //}

        return $t;
    }
}
