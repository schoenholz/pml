<?php

namespace App\Task\Song;

class UpdateSkipCount extends AbstractTask
{
    public function run()
    {
        $this->execSql("
            UPDATE song

            LEFT JOIN (
                SELECT
                    f.song_id,
                    SUM(mft.count) AS touch_count_sum
                
                FROM file f
                
                INNER JOIN meta_file mf
                ON mf.file_id = f.id
                
                INNER JOIN meta_file_touch mft 
                ON mft.meta_file_id = mf.id
                AND mft.type = 'skip'
                
                GROUP BY
                    f.song_id
            ) AS tmp
            ON tmp.song_id = song.id
            
            SET song.skip_count = IFNULL(tmp.touch_count_sum, 0)
        ");
    }
}
