<?php

namespace App\Command;

use App\Entity\Song;
use App\Entity\SongArtist;
use App\Entity\Work;
use App\Entity\WorkHasSong;
use App\Repository\WorkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class MergeWorksCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:works:merge')
            ->setDescription('Merge works.')
            ->addArgument('primary_work_id', InputArgument::REQUIRED)
            ->addArgument('duplicate_work_id', InputArgument::REQUIRED)
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        /** @var WorkRepository $workRepository */
        $workRepository = $this
            ->entityManager
            ->getRepository(Work::class)
        ;

        $primaryWork = $workRepository->find($input->getArgument('primary_work_id'));
        $duplicateWork = $workRepository->find($input->getArgument('duplicate_work_id'));

        if (!$primaryWork) {
            throw new \InvalidArgumentException('Primary Work not found');
        }
        if (!$duplicateWork) {
            throw new \InvalidArgumentException('Duplicate Work not found');
        }

        if ($primaryWork->getId() == $duplicateWork->getId()) {
            throw new \InvalidArgumentException('Primary and duplicate work must not be the same');
        }

        // Primary song info
        $output->writeln('<info>Primary song:</info>');
        $this->printSongInfo(
            $output,
            $primaryWork
                ->getOneWorkHasSongByType(WorkHasSong::TYPE_PRIMARY)
                ->getSong()
        );

        $output->writeln('');

        // Current duplicate songs info
        $i = 0;
        $currentDuplicates = $primaryWork->getWorkHasSongs()->filter(function (WorkHasSong $workHasSong) {
            return $workHasSong->getType() != WorkHasSong::TYPE_PRIMARY;
        });
        /* @var WorkHasSong $workHasSong */
        foreach ($currentDuplicates as $workHasSong) {
            $i++;

            $output->writeln(sprintf('<info>Current duplicate %d/%d:</info>', $i, $currentDuplicates->count()));
            $this->printSongInfo($output, $workHasSong->getSong());

            $output->writeln('');
        }

        // New duplicate songs info
        $newDuplicatesCount = $duplicateWork->getWorkHasSongs()->count();
        $i = 0;
        foreach ($duplicateWork->getWorkHasSongs() as $workHasSong) {
            $i++;
            $output->writeln(sprintf('<info>New duplicate %d/%d:</info>', $i, $newDuplicatesCount));
            $this->printSongInfo($output, $workHasSong->getSong());

            $output->writeln('');
        }

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Merge works?', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<error>Aborting merge.</error>');

            return;
        }

        foreach ($duplicateWork->getWorkHasSongs() as $workHasSong) {
            $workHasSong
                ->setWork($primaryWork)
                ->setType(WorkHasSong::TYPE_DUPLICATE)
            ;
        }

        $this->entityManager->remove($duplicateWork);
        $this->entityManager->flush();

        $output->writeln('<info>Merge complete.</info>');
    }

    private function printSongInfo(
        OutputInterface $output,
        Song $song
    ) {
        $output->writeln(sprintf('Artists: <comment>%s</comment>', implode(
            '</comment>; <comment>',
            $song->getSongArtists()->map(function (SongArtist $songArtist) {
                return $songArtist->getTitle();
            })->toArray()
        )));
        $output->writeln(sprintf('Album: <comment>%s</comment>', $song->getAlbum()));
        $output->writeln(sprintf('Title: <comment>%s</comment>', $song->getTitle()));
        $output->writeln(sprintf('File: <comment>%s</comment>', $song->getFilePathName()));

        if ($song->getIsDeleted()) {
            $output->writeln('<error>Song is marked as deleted!</error>');
        }
    }
}
