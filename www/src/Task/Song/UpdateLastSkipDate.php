<?php

namespace App\Task\Song;

class UpdateLastSkipDate extends AbstractTask
{
    public function run()
    {
        $this->execSql("
            UPDATE song

            LEFT JOIN (
                SELECT
                    f.song_id,
                    MAX(mft.date) AS max_touch_date
                
                FROM file f
                
                INNER JOIN meta_file mf
                ON mf.file_id = f.id
                
                INNER JOIN meta_file_touch mft 
                ON mft.meta_file_id = mf.id
                AND mft.type = 'skip'
                
                WHERE
                    mft.date IS NOT NULL
                
                GROUP BY
                    f.song_id
            ) AS tmp
            ON tmp.song_id = song.id
            
            SET song.last_skip_date = tmp.max_touch_date
        ");
    }
}
