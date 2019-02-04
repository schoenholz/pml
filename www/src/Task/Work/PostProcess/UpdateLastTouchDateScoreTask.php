<?php

namespace App\Task\Work\PostProcess;

use App\Entity\Song;
use App\Task\AbstractPostProcessTask;
use Doctrine\DBAL\FetchMode;

class UpdateLastTouchDateScoreTask extends AbstractPostProcessTask
{
    public function run()
    {
        list($min, $max) = $this->execSqlFetch('
            SELECT 
                MIN(UNIX_TIMESTAMP(last_touch_date)),
                MAX(UNIX_TIMESTAMP(last_touch_date))
                
            FROM song
            
            WHERE
                rating >= :rating
        ', [
            'rating' => Song::MIN_RATING_CONSIDERED_RELEVANT,
        ], FetchMode::NUMERIC);

        if ($max == $min) {
            $this->execSql('
                UPDATE work
                
                SET last_touch_date_score = IF(last_touch_date IS NULL, 1, 100)
            ');
        } else {
            $this->execSql('
                UPDATE work
                
                SET last_touch_date_score = IF(
                    last_touch_date IS NULL, 
                    1,
                    LEAST(
                        100, 
                        ROUND(((UNIX_TIMESTAMP(last_touch_date) - :min_time) / (:max_time - :min_time) * 98) + 2)
                    )
                )
            ', [
                'min_time' => $min,
                'max_time' => $max,
            ]);

            $this->execSql('
                UPDATE work
            
                SET best_last_touch_date_score = last_touch_date_score,
                    best_last_touch_date_score_date = NOW()
            
                WHERE
                    best_last_touch_date_score < last_touch_date_score
            ');
        }
    }
}
