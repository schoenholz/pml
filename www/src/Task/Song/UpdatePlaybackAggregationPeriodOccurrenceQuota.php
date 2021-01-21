<?php

namespace App\Task\Song;

class UpdatePlaybackAggregationPeriodOccurrenceQuota extends AbstractTask
{
    public function run()
    {
        $this->execSql("
            UPDATE song

            LEFT JOIN (
                SELECT
                    tmp.song_id,
                --  tmp.possible_aggregations,
                --  song_aggregations.occurences,
                    IF(
                        tmp.possible_aggregations > 0,
                        IFNULL(song_aggregations.occurences, 0) / tmp.possible_aggregations,
                        0
                    ) AS quota
                    
                FROM (
                    SELECT
                        s.id AS song_id,
                        SUM(IF(s.added_date <= all_aggregations.to_date, 1, 0)) AS possible_aggregations
                
                    FROM song s
                
                    INNER JOIN (
                        SELECT
                            to_date
                        FROM playback_aggregation
                        GROUP BY
                            period,
                            to_date
                    ) AS all_aggregations
                
                    GROUP BY s.id
                ) AS tmp
                
                LEFT JOIN (
                    SELECT
                        song_id,
                        COUNT(1) AS occurences
                    FROM playback_aggregation
                    GROUP BY
                        song_id
                ) AS song_aggregations
                ON tmp.song_id = song_aggregations.song_id
            ) AS quotas
            ON quotas.song_id = song.id
            
            SET song.playback_aggregation_period_occurrence_quota = IFNULL(quotas.quota, 0)
        ");
    }
}
