<?php

namespace App\Task\Song;

class UpdateFirstTouchDate extends AbstractTask
{
    public function run()
    {
        $this->execSql("
            UPDATE song

            LEFT JOIN (
                SELECT
                    f.song_id,
                    MIN(mft.date) AS min_touch_date
                
                FROM file f
                
                INNER JOIN meta_file mf
                ON mf.file_id = f.id
                
                INNER JOIN meta_file_touch mft 
                ON mft.meta_file_id = mf.id
                
                WHERE
                    mft.date IS NOT NULL
                
                GROUP BY
                    f.song_id
            ) AS tmp
            ON tmp.song_id = song.id
            
            SET song.first_touch_date = tmp.min_touch_date
        ");
    }
}
