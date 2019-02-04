<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdateDaysSinceFirstTouchTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work 
            
            SET days_since_first_touch = GREATEST(1, DATEDIFF(NOW(), first_touch_date))
    
            WHERE 
                first_touch_date IS NOT NULL
        ');
    }
}
