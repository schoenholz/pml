<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Song;
use App\Repository\SongRepository;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DuplicatesController extends AbstractController
{
    /**
     * @Route("/duplicates", name="app_duplicates")
     */
    public function duplicates(EntityManagerInterface $entityManager): Response
    {
        /*
        $stmt = $entityManager->getConnection()->prepare("
            SELECT
                tmp.a_song_id,
                tmp.b_song_id,
                f1.is_synthetic AS a_is_synthetic,
                f2.is_synthetic AS b_is_synthetic,
                tmp.artist,
                tmp.title,
                f1.file_path_name AS a_file_path_name,
                f2.file_path_name AS b_file_path_name
            FROM (
                SELECT
                    fs1.song_id AS a_song_id,
                    fs1.file_id AS a_file_id,
                    fs2.song_id AS b_song_id,
                    fs2.file_id AS b_file_id,
                    SUM(fs2.file_is_synthetic) = COUNT(1) AS b_is_synthetic,
                    GROUP_CONCAT(fs1.artist ORDER BY fs1.artist SEPARATOR ', ') AS artist,
                    MIN(fs1.title) AS title
                
                FROM flat_song fs1
                
                INNER JOIN flat_song fs2
                ON fs2.artist = fs1.artist
                AND fs2.title = fs1.title
                AND fs2.song_id > fs1.song_id
                
                -- WHERE
                --     fs1.file_is_synthetic = 0
                
                GROUP BY
                    fs1.song_id,
                    fs1.file_id,
                    fs2.song_id,
                    fs2.file_id
                    
                ORDER BY NULL
            ) AS tmp
            
            INNER JOIN file f1
            ON f1.id = tmp.a_file_id
            
            INNER JOIN file f2
            ON f2.id = tmp.b_file_id
            
            WHERE
                EXISTS(
                    SELECT 1
                    
                    FROM meta_file mf 
                    
                    WHERE 
                        mf.is_deleted = 0
                        AND mf.file_id = f1.id
                        
                    LIMIT 1
                )
            
            ORDER BY
                GREATEST(f2.id, f1.id) DESC
        ");
        */
        $stmt = $entityManager->getConnection()->prepare("
            SELECT
                sdp.song_a_id AS a_song_id,
                sdp.song_b_id AS b_song_id,
                sdp.artist,
                sdp.title,
                f1.file_path_name AS a_file_path_name,
                f1.is_synthetic AS a_is_synthetic,
                f2.file_path_name AS b_file_path_name,
                f2.is_synthetic AS b_is_synthetic
            
            FROM song_duplicate_proposal sdp
            
            INNER JOIN file f1
            ON f1.song_id = sdp.song_a_id
            AND f1.song_relation = 'primary'
            
            INNER JOIN file f2
            ON f2.song_id = sdp.song_b_id
            AND f2.song_relation = 'primary'
            
            WHERE
                sdp.is_dismissed = 0
                AND EXISTS (
                    SELECT 1
                    FROM meta_file mf
                    WHERE
                        mf.file_id = f1.id
                        AND mf.is_deleted = 0
                        -- todo Use only MetaFiles of PRIMARY Lib
                    LIMIT 1
                )
                AND EXISTS (
                    SELECT 1
                    FROM meta_file mf
                    WHERE
                        mf.file_id = f2.id
                        AND mf.is_deleted = 0
                        -- todo Use only MetaFiles of PRIMARY Lib
                    LIMIT 1
                )
                
            ORDER BY
                sdp.title,
                sdp.artist
        ");
        $stmt->execute();

        return $this->render('Duplicates/duplicates.html.twig', [
            'duplicates' => $stmt->fetchAll(FetchMode::ASSOCIATIVE),
        ]);
    }

    /**
     * @Route("/duplicates/merge/{songAId}/{songBId}", name="app_duplicates.merge")
     */
    public function merge(
        int $songAId,
        int $songBId,
        EntityManagerInterface $entityManager,
        SongRepository $songRepository
    ): Response {
        /* @var Song $songA */
        $songA = $songRepository
            ->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter(':id', $songAId)
            ->getQuery()
            ->getSingleResult()
        ;
        /* @var Song $songB */
        $songB = $songRepository
            ->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter(':id', $songBId)
            ->getQuery()
            ->getSingleResult()
        ;

        $primaryFileA = $songA->getPrimaryFile();
        $primaryFileB = $songB->getPrimaryFile();

        if (
            $primaryFileA->getIsSynthetic()
            && !$primaryFileB->getIsSynthetic()
        ) {
            throw new \RuntimeException(sprintf('Trying to use synthetic file %d as primary song', $primaryFileA->getId()));
        }

        $songBHasNonDeleteMetaFile = false;

        foreach ($songB->getFiles() as $file) {
            if ($file->getIsSynthetic()) {
                continue;
            }

            foreach ($file->getMetaFiles() as $metaFile) {
                if (!$metaFile->getIsDeleted()) {
                    $songBHasNonDeleteMetaFile = true;

                    break 2;
                }
            }
        }

        if ($songBHasNonDeleteMetaFile) {
            while (true) {
                foreach ($songA->getFiles() as $file) {
                    foreach ($file->getMetaFiles() as $metaFile) {
                        if (!$metaFile->getIsDeleted()) {
                            break 3;
                        }
                    }
                }

                throw new \RuntimeException(sprintf('Song %d has no existing file.', $songA->getId()));
            }
        }

        foreach ($songB->getFiles() as $file) {
            $file->setSong($songA);
            $file->setSongRelation(File::SONG_RELATION_DUPLICATE);
        }

        $entityManager->remove($songB);
        $entityManager->flush();

        return new Response(sprintf(
            'Ok. Merged song %d into %d.',
            $songBId,
            $songA->getId()
        ));
    }
}
