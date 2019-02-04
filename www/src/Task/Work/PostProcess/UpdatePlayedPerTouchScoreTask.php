<?php

namespace App\Task\Work\PostProcess;

use App\Entity\Song;
use App\Task\AbstractPostProcessTask;
use Doctrine\DBAL\FetchMode;

class UpdatePlayedPerTouchScoreTask extends AbstractPostProcessTask
{
    public function run()
    {
        list($min, $max) = $this->execSqlFetch('
            SELECT 
                MIN(play_count / touch_count),
                MAX(play_count / touch_count)
                
            FROM song
            
            WHERE
                rating >= :rating
                AND touch_count > 0
        ', [
            'rating' => Song::MIN_RATING_CONSIDERED_RELEVANT,
        ], FetchMode::NUMERIC);

        if ($max == $min) {
            $this->execSql('
                UPDATE work
                
                SET played_per_touch_score = 1
            ');
        } else {
            $this->execSql('
                UPDATE work

                SET played_per_touch_score = 
                    IF (
                        touch_count = 0,
                        1,
                        LEAST(
                            100, 
                            ROUND(((play_count / touch_count - :min) / (:max - :min) * 98) + 2)
                        )
                    )
            ', [
                'min' => $min,
                'max' => $max,
            ]);

            $this->execSql('
                UPDATE work
            
                SET best_played_per_touch_score = played_per_touch_score,
                    best_played_per_touch_score_date = NOW()
            
                WHERE
                    best_played_per_touch_score < played_per_touch_score
            ');
        }
    }
}
