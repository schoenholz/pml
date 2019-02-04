<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdatePlayedPerTouchQuotaTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work 
                
            SET played_per_touch_quota = play_count / GREATEST(touch_count, 1)
        ');
    }
}
