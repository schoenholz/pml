<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdateLastPlayDateTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work w

            SET last_play_date = (
                SELECT MAX(s.last_play_date)
                FROM work_has_song whs
                INNER JOIN song s
                ON s.id = whs.song_id
                WHERE
                    whs.work_id = w.id
            )
        ');
    }
}
