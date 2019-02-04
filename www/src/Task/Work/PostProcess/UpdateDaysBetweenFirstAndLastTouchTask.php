<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdateDaysBetweenFirstAndLastTouchTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work 
            
            SET days_between_first_and_last_touch = GREATEST(1, DATEDIFF(last_touch_date, first_touch_date))
    
            WHERE 
                first_touch_date IS NOT NULL
                AND last_touch_date IS NOT NULL
        ');
    }
}
