<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdatePlayCountTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work w

            SET w.play_count = IFNULL((
                SELECT SUM(s.play_count)
                
                FROM work_has_song whs 
                
                INNER JOIN song s 
                ON s.id = whs.song_id
                
                WHERE
                    whs.work_id = w.id
            ), 0)
        ');
    }
}
