<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdateFirstPlayDateTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work w

            SET first_play_date = (
                SELECT MIN(s.first_play_date)
                FROM work_has_song whs
                INNER JOIN song s
                ON s.id = whs.song_id
                WHERE
                    whs.work_id = w.id
            )
        ');
    }
}
