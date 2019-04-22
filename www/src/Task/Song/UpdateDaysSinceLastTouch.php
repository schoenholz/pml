<?php

namespace App\Task\Song;

class UpdateDaysSinceLastTouch extends AbstractTask
{
    public function run()
    {
        $this->execSql("
            UPDATE song

            SET days_since_last_touch = GREATEST(1, DATEDIFF(NOW(), last_touch_date)) 
        ");
    }
}
