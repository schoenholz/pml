<?php

namespace App\Task\Song;

class UpdateAddedDate extends AbstractTask
{
    public function run()
    {
        $this->execSql("
            UPDATE song

            INNER JOIN (
                SELECT
                    f.song_id,
                    MIN(mf.added_date) AS min_added_date
                
                FROM file f
                
                INNER JOIN meta_file mf
                ON mf.file_id = f.id
                
                GROUP BY
                    f.song_id
            ) AS tmp
            ON tmp.song_id = song.id
            
            SET song.added_date = tmp.min_added_date
        ");
    }
}
