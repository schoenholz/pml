<?php

namespace App\Task\Work\PostProcess;

use App\Entity\Song;
use App\Task\AbstractPostProcessTask;
use Doctrine\DBAL\FetchMode;

class UpdatePlayCountScoreTask extends AbstractPostProcessTask
{
    public function run()
    {
        list($min, $max) = $this->execSqlFetch('
            SELECT 
                MIN(play_count),
                MAX(play_count)
                
            FROM song
            
            WHERE
                rating >= :rating
        ', [
            'rating' => Song::MIN_RATING_CONSIDERED_RELEVANT,
        ], FetchMode::NUMERIC);

        if ($max == $min) {
            $this->execSql('
                UPDATE work
                
                SET play_count_score = 1
            ');
        } else {
            $this->execSql('
                UPDATE work

                SET play_count_score = LEAST(
                    100, 
                    ROUND(((play_count - :min_play_count) / (:max_play_count - :min_play_count) * 99) + 1)
                )
            ', [
                'min_play_count' => $min,
                'max_play_count' => $max,
            ]);

            $this->execSql('
                UPDATE work
            
                SET best_play_count_score = play_count_score,
                    best_play_count_score_date = NOW()
            
                WHERE
                    best_play_count_score < play_count_score
            ');
        }
    }
}
