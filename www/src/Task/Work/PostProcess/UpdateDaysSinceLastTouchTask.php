<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdateDaysSinceLastTouchTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work 
            
            SET days_since_last_touch = GREATEST(1, DATEDIFF(NOW(), last_touch_date))
    
            WHERE 
                last_touch_date IS NOT NULL
        ');
    }
}
