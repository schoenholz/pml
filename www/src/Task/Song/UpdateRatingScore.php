<?php

namespace App\Task\Song;

use App\Repository\MetaLibRepository;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;

class UpdateRatingScore extends AbstractTask
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

        $this->execSql("
            UPDATE song

            set rating_score = IFNULL((rating - :min) / (:max - :min) * 100, 0)
        ", [
            'min' => $min,
            'max' => $max,
        ]);

        $this->execSql('
            UPDATE song 
            
            SET best_rating_score = rating_score,
                best_rating_score_date = NOW()
                
            WHERE
                best_rating_score < rating_score
        ');
    }
}
