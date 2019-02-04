<?php

namespace App\Task\Song\PostProcess;

use App\Entity\Song;
use App\Task\AbstractPostProcessTask;

class UpdateTouchCountTask extends AbstractPostProcessTask
{
    public function run()
    {
        $q = $this
            ->entityManager
            ->createQueryBuilder()
            ->update(Song::class, 'Song')
            ->set('Song.touchCount', 'Song.playCount + Song.skipCount')
            ->getQuery()
        ;
        $q->execute();
    }
}
