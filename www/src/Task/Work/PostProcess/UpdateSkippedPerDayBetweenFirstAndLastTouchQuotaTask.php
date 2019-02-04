<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdateSkippedPerDayBetweenFirstAndLastTouchQuotaTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work 
            
            SET skipped_per_day_between_first_and_last_touch_quota = skip_count / GREATEST(1, days_between_first_and_last_touch)
        ');
    }
}
