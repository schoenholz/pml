<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDuplicateProposalsCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:lib:create-duplicate-proposals')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $con = $this->entityManager->getConnection();

        $output->writeln('Searching for duplicates...');

        $duplicatesStmt = $con->prepare("
            INSERT INTO song_duplicate_proposal (song_a_id, song_b_id, is_dismissed, artist, title) 

            SELECT
                IF(MAX(fs1.file_is_synthetic) = 1 AND MAX(fs2.file_is_synthetic) = 0, fs2.song_id, fs1.song_id) AS a_song_id,
                IF(MAX(fs1.file_is_synthetic) = 1 AND MAX(fs2.file_is_synthetic) = 0, fs1.song_id, fs2.song_id) AS b_song_id,
                0,
                SUBSTR(GROUP_CONCAT(DISTINCT fs1.artist ORDER BY fs1.artist SEPARATOR ', '), 1, 255) AS artist,
                MIN(fs1.title) AS title
            
            FROM flat_song fs1
            
            INNER JOIN flat_song fs2
            ON (
                fs2.artist = fs1.artist 
                OR fs2.artist = CONCAT('DJ ', fs1.artist) 
                OR CONCAT('DJ ', fs2.artist) = fs1.artist
                OR fs2.artist = CONCAT('MC ', fs1.artist) 
                OR CONCAT('MC ', fs2.artist) = fs1.artist
            )
            AND fs2.title = fs1.title
            AND fs2.song_id > fs1.song_id
            
            GROUP BY
                fs1.song_id,
                fs2.song_id
            
            ON DUPLICATE KEY UPDATE
                artist=VALUES(artist),
                title=VALUES(title)
        ");
        $duplicatesStmt->execute();

        $output->writeln(sprintf('Created or updated <info>%d</info> proposals.', $duplicatesStmt->rowCount()));
    }
}
