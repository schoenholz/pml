<?php

namespace App\Task\Song\PostProcess;

use App\Task\AbstractPostProcessTask;

class UpdateDeletionDateTask extends AbstractPostProcessTask
{
    public function run()
    {
        $this->execSql('
           UPDATE song 
            
            SET deletion_date = NULL
    
            WHERE 
                deletion_date IS NOT NULL
                AND is_deleted = 0
        ');
        $this->execSql('
            UPDATE song 
            
            SET deletion_date = NOW()
    
            WHERE 
                deletion_date IS NULL
                AND is_deleted = 1
        ');
    }
}
