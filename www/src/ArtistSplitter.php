<?php

namespace App;

use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;

class ArtistSplitter
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array
     */
    private $protectedArtists;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function split(string $artist, string $trackTitle = null): array
    {
        $artists = [];

        // Extract artists from title
        if ($trackTitle !== null) {
            $titleArtistsPattern = '/\(\s*(?:featuring|feat\.{0,1}|ft\.)\s+([^)]+)\s*\)/i';
            preg_match($titleArtistsPattern, $trackTitle, $trackTitleArtists);

            if (!empty($trackTitleArtists[1])) {
                $artists = $this->split($trackTitleArtists[1]);
            }
        }

        $delims = '(?:\s+feat(?:\.)*\s+)|'
            . '(?:\s+featuring\s+)|'
            . '(?:\s+vs\.\s+)|'
            . '(?:\s+pres\.\s+)|'
            . '(?:\s+presents\s+)|'
            . '(?:\s+and\s+)|'
            . '(?:,)|'
            . '(?:;)|'
            . '(?:\s+&\s+)|'
            . '(?:\s+x\s+)'
        ;

        foreach ($this->getProtectedArtists() as $protectedArtist) {
            if ($artist == $protectedArtist) {
                $artists[] = $artist;

                return $this->filterArtists($artists);
            } elseif (($pos = mb_strpos($artist, $protectedArtist)) !== false) {
                $prefix = mb_substr($artist, 0, $pos);
                $suffix = mb_substr($artist, $pos + mb_strlen($protectedArtist), mb_strlen($artist));
                $prefixIsEmpty = empty(trim($prefix));
                $suffixIsEmpty = empty(trim($suffix));

                if (
                    (
                        $prefixIsEmpty
                        || preg_match('/' . $delims . '$/i', $prefix, $matchPrefix)
                    ) && (
                        $suffixIsEmpty
                        || preg_match('/^' . $delims . '/i', $suffix, $matchSuffix)
                    )
                ) {
                    $artists[] = $protectedArtist;

                    if ($prefixIsEmpty && $suffixIsEmpty) {
                        return $this->filterArtists($artists);
                    } elseif ($prefixIsEmpty) {
                        $artist = mb_substr($suffix, mb_strlen($matchSuffix[0]));
                    } elseif ($suffixIsEmpty) {
                        $artist = mb_substr($prefix, 0, mb_strlen($prefix) - mb_strlen($matchPrefix[0]));
                    } else {
                        $artist = mb_substr($prefix, 0, mb_strlen($prefix) - mb_strlen($matchPrefix[0]))
                            . ';'
                            . mb_substr($suffix, mb_strlen($matchSuffix[0]));
                    }
                }
            }
        }

        $chunks = preg_split('/' . $delims . '/i', $artist);

        return $this->filterArtists(array_merge($chunks, $artists));
    }

    protected function filterArtists(array $arr): array
    {
        return array_filter(array_unique(array_map('trim', $arr)), function(string $v): bool {
            return $v !== '';
        });
    }

    protected function getProtectedArtists(): array
    {
        if ($this->protectedArtists === null) {
            $this->protectedArtists = $this->fetchProtectedArtists();
        }

        return $this->protectedArtists;
    }

    private function fetchProtectedArtists(): array {
        $stmt = $this->entityManager->getConnection()->prepare("
            SELECT DISTINCT mfa.title

            FROM meta_file_artist mfa
            
            INNER JOIN meta_file mf 
            ON mf.id = mfa.meta_file_id
            
            INNER JOIN file f
            ON f.id = mf.file_id
            
            WHERE
                f.is_synthetic = 0
                AND (
                    INSTR(mfa.title, '&') > 0
                    OR INSTR(mfa.title, ',') > 0
                    OR INSTR(mfa.title, ';') > 0
                    OR INSTR(mfa.title, ' feat. ') > 0
                    OR INSTR(mfa.title, ' vs. ') > 0
                )
            
            ORDER BY
                LENGTH(mfa.title) DESC
        ");
        $stmt->execute();

        return $stmt->fetchAll(FetchMode::COLUMN);
    }
}
