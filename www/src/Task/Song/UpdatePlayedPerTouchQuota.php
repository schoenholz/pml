<?php

namespace App\Task\Song;

class UpdatePlayedPerTouchQuota extends AbstractTask
{
    public function run()
    {
        $this->execSql("
            UPDATE song
            SET played_per_touch_quota = IF(touch_count = 0, 0,  play_count / touch_count)
        ");
    }
}
