<?php

namespace App\Task\Song;

class UpdateDaysBetweenFirstAndLastTouch extends AbstractTask
{
    public function run()
    {
        $this->execSql("
            UPDATE song

            SET days_between_first_and_last_touch = GREATEST(1, DATEDIFF(last_touch_date, first_touch_date)) 
        ");
    }
}
