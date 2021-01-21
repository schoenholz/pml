<?php

namespace App\Task\Song;

use Doctrine\DBAL\FetchMode;

class UpdatePlayCountScore extends AbstractTask
{
    public function run()
    {
        list($min, $max) = $this->execSqlFetch('
            SELECT
                MIN(play_count) AS min,
                MAX(play_count) AS max
            
            FROM song
            
            WHERE
                play_count IS NOT NULL 
                AND rating >= 80
        ', [], FetchMode::NUMERIC);

        $this->execSql("
            UPDATE song

            SET play_count_score = IFNULL((play_count - :min) / (:max - :min) * 100, 0)
        ", [
            'min' => $min,
            'max' => $max,
        ]);

        $this->execSql('
            UPDATE song 
            
            SET best_play_count_score = play_count_score,
                best_play_count_score_date = NOW()
                
            WHERE
                best_play_count_score < play_count_score
        ');
    }
}
