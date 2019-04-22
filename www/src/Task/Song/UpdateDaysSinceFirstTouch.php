<?php

namespace App\Task\Song;

class UpdateDaysSinceFirstTouch extends AbstractTask
{
    public function run()
    {
        $this->execSql("
            UPDATE song

            SET days_since_first_touch = GREATEST(1, DATEDIFF(NOW(), first_touch_date)) 
        ");
    }
}
