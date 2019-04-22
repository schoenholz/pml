<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlattenSongsCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:lib:flatten_songs')
            ->setDescription('Flatten songs.')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->output = $output;

        $this->purgeTable();
        $this->flattenSongs();
    }

    private function flattenSongs()
    {
        $this->output->writeln('Flattening songs.');

        $this
            ->entityManager
            ->getConnection()
            ->prepare("
                INSERT INTO flat_song (song_id, file_id, artist, title, file_is_synthetic)
                
                SELECT
                    s.id AS song_id,
                    f_primary.id AS primary_file_id,
                    mfa.title_normalized AS artist_title,
                    mf.title_normalized AS track_title,
                    f_primary.is_synthetic
                
                FROM song s
                
                INNER JOIN file f_primary
                ON f_primary.song_id = s.id

                INNER JOIN file f_all
                ON f_all.song_id = s.id
                
                INNER JOIN meta_file mf
                ON mf.file_id = f_all.id
                
                INNER JOIN meta_file_artist mfa
                ON mfa.meta_file_id = mf.id
                
                GROUP BY
                    f_primary.id,
                    song_id,
                    artist_title,
                    track_title
                ;
            ")
            ->execute()
        ;
    }

    private function purgeTable()
    {
        $this->output->writeln('Purging table.');

        $this
            ->entityManager
            ->getConnection()
            ->prepare('TRUNCATE TABLE flat_song')
            ->execute()
        ;
    }
}