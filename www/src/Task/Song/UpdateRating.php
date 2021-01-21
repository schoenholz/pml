<?php

namespace App\Task\Song;

use App\Repository\MetaLibRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateRating extends AbstractTask
{
    /**
     * @var MetaLibRepository
     */
    private $metaLibRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        MetaLibRepository $metaLibRepository
    ) {
        parent::__construct($entityManager);

        $this->metaLibRepository = $metaLibRepository;
    }

    public function run()
    {
        $this->execSql("
            UPDATE song

            LEFT JOIN (
                SELECT
                    f.song_id,
                    MAX(mf.rating) AS rating
                
                FROM file f
                
                INNER JOIN meta_file mf
                ON mf.file_id = f.id
                AND mf.meta_lib_id = :meta_lib_id
                
                WHERE f.song_relation = 'primary'
                
                GROUP BY
                    f.song_id
            ) AS tmp
            ON tmp.song_id = song.id
            
            SET song.rating = tmp.rating
        ", [
            'meta_lib_id' => $this
                ->metaLibRepository
                ->requireOneBy('name', 'MediaMonkey 4 Dell Laptop')
                ->getId(),
        ]);
    }
}
