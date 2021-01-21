<?php

namespace App;

class ArtistTitleNormalizer
{
    /**
     * @var \Transliterator
     */
    private $transliterator;

    public function normalize(string $title): string
    {
        // todo What happens to ´, `, ", ',

        if ($this->transliterator === null) {
            $this->transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');;
        }

        return mb_strtolower($this->transliterator->transliterate($title));
    }
}