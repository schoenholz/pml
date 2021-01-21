<?php

namespace App\Task\Song;

use Doctrine\DBAL\FetchMode;

class UpdateLastTouchDateScore extends AbstractTask
{
    public function run()
    {
        list($min, $max) = $this->execSqlFetch('
            SELECT
                DATEDIFF(NOW(), MIN(last_touch_date)) AS min,
                DATEDIFF(NOW(), MAX(last_touch_date)) AS max
            
            FROM song
            
            WHERE
                last_touch_date IS NOT NULL
                AND rating >= 80
        ', [], FetchMode::NUMERIC);

        $this->execSql("
            UPDATE song

            set last_touch_date_score = IFNULL((DATEDIFF(NOW(), last_touch_date) - :min) / (:max - :min) * 100, 0)
        ", [
            'min' => $min,
            'max' => $max,
        ]);
    }
}
