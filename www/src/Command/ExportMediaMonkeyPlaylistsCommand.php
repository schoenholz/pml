<?php

namespace App\Command;

use App\MediaMonkeyDatabase;
use App\Meta\Lib\Manager\MediaMonkeyDatabaseManager;
use App\PlaylistManager;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportMediaMonkeyPlaylistsCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MediaMonkeyDatabaseManager
     */
    private $mediaMonkeyDatabaseManager;

    /**
     * @var MediaMonkeyDatabase
     */
    private $mediaMonkeyDatabase;

    public function __construct(
        EntityManagerInterface $entityManager,
        MediaMonkeyDatabase $mediaMonkeyDatabase,
        MediaMonkeyDatabaseManager $mediaMonkeyDatabaseManager
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->mediaMonkeyDatabase = $mediaMonkeyDatabase;
        $this->mediaMonkeyDatabaseManager = $mediaMonkeyDatabaseManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:playlist:export:media-monkey')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $localPlaylistName = 'Duplicates';
        $remotePlaylistName = '$_' . $localPlaylistName;
        $remotePlaylistId = $this->mediaMonkeyDatabaseManager->fetchPlaylistId($remotePlaylistName);

        $con = $this->entityManager->getConnection();
        $stmt = $con->prepare("
            SELECT
                mf.external_id
            
            FROM playlist p
            
            INNER JOIN meta_lib ml
            ON ml.name = 'MediaMonkey 4 Dell Laptop'
            
            INNER JOIN playlist_item pi
            ON pi.playlist_id = p.id
            
            INNER JOIN file f
            ON f.id = pi.file_id
            
            INNER JOIN meta_file mf
            ON mf.file_id = f.id
            AND mf.meta_lib_id = ml.id
            
            WHERE
                p.name = 'Duplicates'
                AND mf.is_deleted = 0
            
            ORDER BY
                pi.position ASC
        ");
        $stmt->execute();
        $remoteSongIds = $stmt->fetchAll(FetchMode::COLUMN);

        $remoteCon = $this->mediaMonkeyDatabase->getConnection();

        // Delete old entries
        $stmtDelete = $remoteCon->prepare('DELETE FROM PlaylistSongs WHERE IDPlaylist = ?');
        $stmtDelete->execute([
            $remotePlaylistId,
        ]);

        // Add new entries
        $i = 0;
        foreach ($remoteSongIds as $remoteSongId) {
            $i ++;
            $stmtInsert = $remoteCon->prepare('INSERT INTO PlaylistSongs (IDPlaylist, IDSong, SongOrder) VALUES (?, ?, ?)');
            $stmtInsert->execute([
                $remotePlaylistId,
                $remoteSongId,
                $i
            ]);
        }
    }
}
