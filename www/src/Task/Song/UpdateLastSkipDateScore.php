<?php

namespace App\Task\Song;

use Doctrine\DBAL\FetchMode;

class UpdateLastSkipDateScore extends AbstractTask
{
    public function run()
    {
        list($min, $max) = $this->execSqlFetch('
            SELECT
                DATEDIFF(NOW(), MIN(last_skip_date)) AS min,
                DATEDIFF(NOW(), MAX(last_skip_date)) AS max
            
            FROM song
            
            WHERE
                last_skip_date IS NOT NULL
                AND rating >= 80
        ', [], FetchMode::NUMERIC);

        $this->execSql("
            UPDATE song

            set last_skip_date_score = IFNULL((DATEDIFF(NOW(), last_skip_date) - :min) / (:max - :min) * 100, 0)
        ", [
            'min' => $min,
            'max' => $max,
        ]);
    }
}
