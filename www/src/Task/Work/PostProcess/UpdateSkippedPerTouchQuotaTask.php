<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdateSkippedPerTouchQuotaTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work 
                
            SET skipped_per_touch_quota = skip_count / GREATEST(touch_count, 1)
        ');
    }
}
