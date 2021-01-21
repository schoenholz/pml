<?php

namespace App\Task\Song;

use Doctrine\DBAL\FetchMode;

class UpdateMaxPlaybackPercentageScore extends AbstractTask
{
    public function run()
    {
        list($min, $max) = $this->execSqlFetch('
            SELECT
                MIN(max_playback_percentage) AS min,
                MAX(max_playback_percentage) AS max
            
            FROM song
            
            WHERE
                max_playback_percentage IS NOT NULL 
                AND rating >= 80
        ', [], FetchMode::NUMERIC);

        $this->execSql("
            UPDATE song

            set max_playback_percentage_score = IFNULL((max_playback_percentage - :min) / (:max - :min) * 100, 0)
        ", [
            'min' => $min,
            'max' => $max,
        ]);
    }
}
