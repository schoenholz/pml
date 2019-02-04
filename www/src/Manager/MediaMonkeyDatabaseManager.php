<?php

namespace App\Manager;

use App\MediaMonkeyDatabase;

class MediaMonkeyDatabaseManager
{
    /**
     * @var MediaMonkeyDatabase
     */
    private $mediaMonkeyDatabase;

    public function __construct(MediaMonkeyDatabase $mediaMonkeyDatabase)
    {
        $this->mediaMonkeyDatabase = $mediaMonkeyDatabase;
    }

    public function fetchSongIds(): array
    {
        $stmt = $this
            ->mediaMonkeyDatabase
            ->getConnection()
            ->prepare('SELECT ID FROM Songs')
        ;
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function fetchSongData(int $songId): array
    {
        $stmt = $this
            ->mediaMonkeyDatabase
            ->getConnection()
            ->prepare('
                SELECT
                    Songs.SongTitle AS title,
                    Songs.Publisher AS publisher,
                    Songs.Album AS album,
                    Songs.DiscNumber AS disc_number,
                    Songs.TrackNumber AS track_number,
                    Songs.Year AS date,
                    Songs.Year AS year,
                    Songs.Rating AS rating,
                    Songs.PlayCounter AS play_count,
                    Songs.SkipCount AS skip_count,
                    Songs.Bitrate AS bitrate,
                    Songs.SamplingFrequency AS sampling_frequency,
                    Songs.SongPath AS file_path_name,
                    Songs.BPM AS bpm,
                    Songs.InitialKey AS initial_key,
                    datetime(julianday(Songs.DateAdded) + julianday("1899-12-30"), "localtime") AS added_date,
                    CASE
                        WHEN LastTimePlayed > 0 
                        THEN datetime(julianday(Songs.LastTimePlayed) + julianday("1899-12-30"), "localtime") 
                        ELSE NULL 
                    END AS last_played_date,
                    (
                        SELECT
                            MIN(datetime(julianday(Played.PlayDate) + julianday("1899-12-30"), "localtime"))
                        FROM Played
                        WHERE 
                            Played.IDSong = Songs.ID
                    ) AS first_played_date
                    
                FROM Songs
                
                WHERE 
                    Songs.ID = :id
            ')
        ;
        $stmt->execute([
            'id' => $songId,
        ]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (empty($data)) {
            throw new \RuntimeException(sprintf('Song with ID "%d" no found in MediaMonkey database"', $songId));
        }

        $data = array_map(function ($item) {
            return $item === ''
                ? null
                : $item;
        }, $data);

        // Disallow "-1" as value
        foreach ([
            'bpm',
            'rating',
        ] as $field) {
            if ($data[$field] == -1) {
                $data[$field] = null;
            }
        }

        // Allow integers or set to NULL
        foreach ([
            'disc_number',
            'sampling_frequency',
            'track_number',
        ] as $field) {
            if (
                !is_int($data[$field])
                && !ctype_digit($data[$field])
            ) {
                // todo Create report
                $data[$field] = null;
            }
        }

        // Convert dates
        foreach ([
            'added_date',
            'first_played_date',
            'last_played_date',
        ] as $field) {
            if (!empty($data[$field])) {
                $data[$field] = new \DateTime($data[$field]);
            } else {
                $data[$field] = null;
            }
        }

        if (
            strlen($data['date']) === 8
            && substr($data['date'], 4, 4) !== '0000'
        ) {
            preg_match('/^(\d{4})(\d{2})(\d{2})$/', $data['date'], $dateMatches);
            $data['date'] = new \DateTime(sprintf('%d-%d-%d', $dateMatches[1], $dateMatches[2], $dateMatches[3]));
        } else {
            $data['date'] = null;
        }

        if (strlen($data['year']) === 8) {
            $data['year'] = substr($data['year'], 0, 4);
        } else {
            $data['year'] = null;
        }

        $data['artists'] = $this->fetchSongArtists($songId);
        $data['genres'] = $this->fetchSongGenres($songId);

        return $data;
    }

    public function fetchSongArtists(int $songId): array
    {
        $stmt = $this
            ->mediaMonkeyDatabase
            ->getConnection()
            ->prepare('
                SELECT Artists.Artist AS title
                
                FROM ArtistsSongs
                
                INNER JOIN Artists 
                ON Artists.ID = ArtistsSongs.IDArtist
                
                WHERE
                    ArtistsSongs.IDSong = :id
                    AND ArtistsSongs.PersonType = 1
            ')
        ;
        $stmt->execute([
            'id' => $songId,
        ]);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function fetchSongGenres(int $songId): array
    {
        $stmt = $this
            ->mediaMonkeyDatabase
            ->getConnection()
            ->prepare('
                SELECT Genres.GenreName AS title
                
                FROM GenresSongs
                
                INNER JOIN Genres
                ON Genres.IDGenre = GenresSongs.IDGenre
                
                WHERE
                    GenresSongs.IDSong = :id
            ')
        ;
        $stmt->execute([
            'id' => $songId,
        ]);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
