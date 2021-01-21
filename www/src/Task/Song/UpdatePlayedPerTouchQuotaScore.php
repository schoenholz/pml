<?php

namespace App\Task\Song;

use Doctrine\DBAL\FetchMode;

class UpdatePlayedPerTouchQuotaScore extends AbstractTask
{
    public function run()
    {
        list($min, $max) = $this->execSqlFetch('
            SELECT
                MIN(played_per_touch_quota) AS min,
                MAX(played_per_touch_quota) AS max
            
            FROM song
            
            WHERE
                played_per_touch_quota IS NOT NULL
                AND rating >= 80
        ', [], FetchMode::NUMERIC);

        $this->execSql("
            UPDATE song

            set played_per_touch_quota_score = IFNULL((played_per_touch_quota - :min) / (:max - :min) * 100, 0)
        ", [
            'min' => $min,
            'max' => $max,
        ]);
    }
}
