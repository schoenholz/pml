<?php

namespace App;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;

class PlaylistManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PlaylistRepository
     */
    private $playlistRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PlaylistRepository $playlistRepository
    ) {
        $this->entityManager = $entityManager;
        $this->playlistRepository = $playlistRepository;
    }

    public function createPlaylist(
        string $name,
        array $fileIds
    ) {
        // Find or create playlist
        $playlist = $this->playlistRepository->findOneBy([
            'name' => 'Duplicates',
        ]);

        if (!$playlist) {
            $playlist = new Playlist();
            $playlist->setName($name);
            $this->entityManager->persist($playlist);
            $this->entityManager->flush();
        }

        // Remove existing playlist items
        $con = $this->entityManager->getConnection();
        $deleteStmt = $con->prepare('DELETE FROM playlist_item WHERE playlist_id = :id');
        $deleteStmt->execute([
            'id' => $playlist->getId(),
        ]);

        // Add new playlist items
        $insertValues = [];
        $inserts = [];
        $i = 0;
        foreach ($fileIds as $fileId) {
            $i ++;
            $insertValues['f_' . $i] = $fileId;
            $insertValues['p_' . $i] = $i;
            $inserts[] = sprintf(' (%d, :f_%d, :p_%d) ', $playlist->getId(), $i, $i);
        }

        if (!empty($inserts)) {
            $sql = 'INSERT INTO playlist_item (playlist_id, file_id, position) VALUES ' . implode(', ', $inserts);
            $insertStmt = $con->prepare($sql);
            $insertStmt->execute($insertValues);
        }
    }
}
