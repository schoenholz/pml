<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;
use Doctrine\DBAL\FetchMode;

class UpdateRatingScoreTask extends AbstractPostProcessTask
{
    public function run()
    {
        list($min, $max) = $this->execSqlFetch('
            SELECT
                MIN(rating) AS min,
                MAX(rating) AS max
            
            FROM song
            
            WHERE
                rating IS NOT NULL
        ', [], FetchMode::NUMERIC);

        if ($max == $min) {
            $this->execSql('
                UPDATE work
                
                SET rating_score = IF(rating IS NULL, 1, 100)
            ');
        } else {
            $this->execSql('
                UPDATE work
                
                SET rating_score = IF(
                    rating IS NULL, 
                    1, 
                    LEAST(
                        100, 
                        ROUND(((rating - :min_rating) / (:max_rating - :min_rating) * 98) + 2)
                    )
                )
            ', [
                'min_rating' => $min,
                'max_rating' => $max,
            ]);

            $this->execSql('
                UPDATE work 
                
                SET best_rating_score = rating_score,
                    best_rating_score_date = NOW()
                    
                WHERE
                    best_rating_score < rating_score
            ');
        }
    }
}
