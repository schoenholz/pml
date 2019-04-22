<?php

namespace App\Command;

use App\Entity\File;
use App\Entity\MetaFile;
use App\Entity\MetaFileArtist;
use App\Entity\MetaFileGenre;
use App\Entity\MetaFileTouch;
use App\Entity\MetaLib;
use App\Entity\Song;
use App\Meta\Lib\Manager\MediaMonkeyDatabaseManager;
use App\MetaFileWriter;
use App\Repository\LastFmPlaybackRepository;
use App\Repository\MetaLibRepository;
use App\Task\Song\SongTaskInterface;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSongsCommand extends Command
{
    /**
     * @var \IteratorAggregate|SongTaskInterface[]
     */
    private $tasks;

    public function __construct(\IteratorAggregate $tasks)
    {
        parent::__construct(null);

        $this->tasks = $tasks;
    }

    protected function configure()
    {
        $this
            ->setName('app:songs:update')
            ->setDescription('Updates song data.')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $output->writeln('<info>Updating songs.</info>');

        $progressBar = new ProgressBar($output, count($this->tasks));
        $progressBar->start();

        foreach ($this->tasks as $task) {
            $task->run();
            $progressBar->advance();
        }

        $progressBar->finish();
        $output->writeln('');
    }
}
