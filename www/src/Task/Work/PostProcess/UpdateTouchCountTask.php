<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdateTouchCountTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work w

            SET touch_count = IFNULL((
                SELECT SUM(s.touch_count)
                FROM work_has_song whs
                INNER JOIN song s
                ON s.id = whs.song_id
                WHERE
                    whs.work_id = w.id
            ), 0)
        ');
    }
}
