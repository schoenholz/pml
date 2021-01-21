<?php

namespace App\Task\Song;

class UpdateMaxPlaybackPercentage extends AbstractTask
{
    public function run()
    {
        $this->execSql("
            UPDATE song

            LEFT JOIN (
                SELECT
                    song_id,
                    MAX(percentage) AS max_percentage
                
                FROM playback_aggregation
                
                WHERE
                    total_count > DATEDIFF(to_date, from_date) * 3
                    AND from_date > '2018-07-29 00:00:00' -- After using last.fm
                
                GROUP BY
                    song_id
            ) AS p
            ON p.song_id = song.id
            
            SET
                song.max_playback_percentage = IFNULL(p.max_percentage, 0),
                song.max_playback_percentage_date = (
                    SELECT MAX(to_date) 
                    FROM playback_aggregation 
                    WHERE
                        song_id = song.id 
                        AND percentage = p.max_percentage
                )
        ");
    }
}
