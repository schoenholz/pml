<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdateSkipCountTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work w

            SET w.skip_count = IFNULL((
                SELECT SUM(s.skip_count)
                
                FROM work_has_song whs 
                
                INNER JOIN song s 
                ON s.id = whs.song_id
                
                WHERE
                    whs.work_id = w.id
            ), 0)
        ');
    }
}
