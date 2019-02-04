<?php

namespace App\Task\Work\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdateDaysInLibraryTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
            UPDATE work 
            
            SET days_in_library = GREATEST(1, DATEDIFF(NOW(), added_date)) 
        ');
    }
}
