<?php

namespace App\Task\Song;

class UpdateDaysInLibrary extends AbstractTask
{
    public function run()
    {
        $this->execSql('
            UPDATE song 
            
            SET days_in_library = GREATEST(1, DATEDIFF(NOW(), IFNULL(added_date, NOW()))) 
        ');
    }
}
