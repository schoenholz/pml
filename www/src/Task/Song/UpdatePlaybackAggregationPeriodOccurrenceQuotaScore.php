<?php

namespace App\Task\Song;

use Doctrine\DBAL\FetchMode;

class UpdatePlaybackAggregationPeriodOccurrenceQuotaScore extends AbstractTask
{
    public function run()
    {
        list($min, $max) = $this->execSqlFetch('
            SELECT
                MIN(playback_aggregation_period_occurrence_quota) AS min,
                MAX(playback_aggregation_period_occurrence_quota) AS max
            
            FROM song
            
            WHERE
                playback_aggregation_period_occurrence_quota IS NOT NULL
                AND rating >= 80
        ', [], FetchMode::NUMERIC);

        $this->execSql("
            UPDATE song

            set playback_aggregation_period_occurrence_quota_score = IFNULL((playback_aggregation_period_occurrence_quota - :min) / (:max - :min) * 100, 0)
        ", [
            'min' => $min,
            'max' => $max,
        ]);
    }
}
