<?php

namespace App\Command;

use App\PlaylistManager;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildDuplicatesPlaylistCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PlaylistManager
     */
    private $playlistManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        PlaylistManager $playlistManager
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->playlistManager = $playlistManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:playlist:build:duplicates')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $con = $this->entityManager->getConnection();
        $stmt = $con->prepare("
            SELECT
                id
            
            FROM file f
            
            WHERE
                f.song_relation = 'duplicate'
            
            ORDER BY
                (
                    SELECT title_normalized
                    FROM meta_file mf
                    WHERE
                        mf.file_id = f.id
                    LIMIT 1
                ) ASC
        ");
        $stmt->execute();
        $fileIds = $stmt->fetchAll(FetchMode::COLUMN);

        $this->playlistManager->createPlaylist('Duplicates', $fileIds);
    }
}
