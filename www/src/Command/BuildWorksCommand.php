<?php

namespace App\Command;

use App\Entity\Song;
use App\Entity\Work;
use App\Entity\WorkHasSong;
use App\Repository\SongRepository;
use App\Task\AbstractPostProcessTask;
use App\Task\Work\PostProcess\UpdateAddedDateTask;
use App\Task\Work\PostProcess\UpdateDaysBetweenFirstAndLastTouchTask;
use App\Task\Work\PostProcess\UpdateDaysInLibraryTask;
use App\Task\Work\PostProcess\UpdateDaysSinceFirstTouchTask;
use App\Task\Work\PostProcess\UpdateDaysSinceLastTouchTask;
use App\Task\Work\PostProcess\UpdateFirstPlayDateTask;
use App\Task\Work\PostProcess\UpdateFirstSkipDateTask;
use App\Task\Work\PostProcess\UpdateFirstTouchDateTask;
use App\Task\Work\PostProcess\UpdateLastPlayDateTask;
use App\Task\Work\PostProcess\UpdateLastSkipDateTask;
use App\Task\Work\PostProcess\UpdateLastTouchDateScoreTask;
use App\Task\Work\PostProcess\UpdateLastTouchDateTask;
use App\Task\Work\PostProcess\UpdatePlayCountScoreTask;
use App\Task\Work\PostProcess\UpdatePlayCountTask;
use App\Task\Work\PostProcess\UpdatePlayedPerDayBetweenFirstAndLastTouchQuotaTask;
use App\Task\Work\PostProcess\UpdatePlayedPerTouchQuotaTask;
use App\Task\Work\PostProcess\UpdatePlayedPerTouchScoreTask;
use App\Task\Work\PostProcess\UpdateRatingScoreTask;
use App\Task\Work\PostProcess\UpdateRatingTask;
use App\Task\Work\PostProcess\UpdateSkipCountTask;
use App\Task\Work\PostProcess\UpdateSkippedPerDayBetweenFirstAndLastTouchQuotaTask;
use App\Task\Work\PostProcess\UpdateSkippedPerTouchQuotaTask;
use App\Task\Work\PostProcess\UpdateTouchCountTask;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildWorksCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var AbstractPostProcessTask[]
     */
    private $postProcessTasks = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        UpdateTouchCountTask $updateTouchCountTask,
        UpdateFirstTouchDateTask $updateFirstTouchDateTask,
        UpdateLastTouchDateTask $updateLastTouchDateTask,
        UpdateDaysBetweenFirstAndLastTouchTask $updateDaysBetweenFirstAndLastTouchTask,
        UpdateDaysSinceFirstTouchTask $updateDaysSinceFirstTouchTask,
        UpdateDaysSinceLastTouchTask $updateDaysSinceLastTouchTask,
        UpdatePlayCountTask $updatePlayCountTask,
        UpdateFirstPlayDateTask $updateFirstPlayDateTask,
        UpdateLastPlayDateTask $updateLastPlayDateTask,
        UpdatePlayedPerTouchQuotaTask $updatePlayedPerTouchQuotaTask,
        UpdateSkipCountTask $updateSkipCountTask,
        UpdateFirstSkipDateTask $updateFirstSkipDateTask,
        UpdateLastSkipDateTask $updateLastSkipDateTask,
        UpdateSkippedPerTouchQuotaTask $updateSkippedPerTouchQuotaTask,
        UpdateAddedDateTask $updateAddedDateTask,
        UpdateDaysInLibraryTask $updateDaysInLibraryTask,
        UpdatePlayedPerDayBetweenFirstAndLastTouchQuotaTask $updatePlayedPerDayBetweenFirstAndLastTouchQuotaTask,
        UpdateSkippedPerDayBetweenFirstAndLastTouchQuotaTask $updateSkippedPerDayBetweenFirstAndLastTouchQuotaTask,
        UpdateRatingTask $updateRatingTask,
        UpdateRatingScoreTask $updateRatingScoreTask,
        UpdateLastTouchDateScoreTask $updateLastTouchDateScoreTask,
        UpdatePlayCountScoreTask $updatePlayCountScoreTask,
        UpdatePlayedPerTouchScoreTask $updatePlayedPerTouchScoreTask
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->postProcessTasks[] = $updateAddedDateTask;
        $this->postProcessTasks[] = $updateDaysInLibraryTask;
        $this->postProcessTasks[] = $updatePlayCountTask;
        $this->postProcessTasks[] = $updateFirstPlayDateTask;
        $this->postProcessTasks[] = $updateLastPlayDateTask;
        $this->postProcessTasks[] = $updateSkipCountTask;
        $this->postProcessTasks[] = $updateFirstSkipDateTask;
        $this->postProcessTasks[] = $updateLastSkipDateTask;
        $this->postProcessTasks[] = $updateTouchCountTask;
        $this->postProcessTasks[] = $updateFirstTouchDateTask;
        $this->postProcessTasks[] = $updateLastTouchDateTask;
        $this->postProcessTasks[] = $updateDaysBetweenFirstAndLastTouchTask;
        $this->postProcessTasks[] = $updatePlayedPerTouchQuotaTask;
        $this->postProcessTasks[] = $updateSkippedPerTouchQuotaTask;
        $this->postProcessTasks[] = $updateDaysSinceFirstTouchTask;
        $this->postProcessTasks[] = $updateDaysSinceLastTouchTask;
        $this->postProcessTasks[] = $updatePlayedPerDayBetweenFirstAndLastTouchQuotaTask;
        $this->postProcessTasks[] = $updateSkippedPerDayBetweenFirstAndLastTouchQuotaTask;
        $this->postProcessTasks[] = $updateRatingTask;
        $this->postProcessTasks[] = $updateRatingScoreTask;
        $this->postProcessTasks[] = $updateLastTouchDateScoreTask;
        $this->postProcessTasks[] = $updatePlayCountScoreTask;
        $this->postProcessTasks[] = $updatePlayedPerTouchScoreTask;
    }

    protected function configure()
    {
        $this
            ->setName('app:works:build')
            ->setDescription('Build work library.')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->output = $output;
        $this->output->writeln('<info>Building works.</info>');

        // Create works for all Songs not associated to a work
        $this->createWorksForNewSongs();

        $this->postProcess();
    }

    private function postProcess()
    {
        $this->output->writeln('<info>Post processing works.</info>');

        $progressBar = new ProgressBar($this->output, count($this->postProcessTasks));
        $progressBar->start();

        foreach ($this->postProcessTasks as $task) {
            $task->run();
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');
    }

    private function createWorksForNewSongs()
    {
        $newSongIds = $this->getSongsWithoutWork();
        $newSongsCount = count($newSongIds);

        $this->output->writeln(sprintf('Found <comment>%d</comment> new songs.', $newSongsCount));

        if ($newSongsCount === 0) {
            return;
        }

        $progressBar = new ProgressBar($this->output, $newSongsCount);
        $progressBar->start();

        /** @var SongRepository $songRepo */
        $songRepo = $this->entityManager->getRepository(Song::class);

        foreach ($newSongIds as $songId) {
            $progressBar->advance();

            $song = $songRepo->findOneBy([
                'id' => $songId,
            ]);

            if (!$song) {
                throw new \RuntimeException(sprintf('Song with ID %d not found', $songId));
            }

            $work = new Work();
            $work
                ->setTouchCount(0)
                ->setDaysBetweenFirstAndLastTouch(0)
                ->setDaysSinceFirstTouch(0)
                ->setDaysSinceLastTouch(0)
                ->setPlayCount(0)
                ->setPlayedPerTouchQuota(0)
                ->setSkipCount(0)
                ->setSkippedPerTouchQuota(0)
                ->setPlayedPerDayBetweenFirstAndLastTouchQuota(0)
                ->setSkippedPerDayBetweenFirstAndLastTouchQuota(0)
                ->setDaysInLibrary(0)
                ->setAddedDate($song->getAddedDate())
                ->setRatingScore(0)
                ->setLastTouchDateScore(0)
                ->setPlayCountScore(0)
                ->setPlayedPerTouchScore(0)
                ->setBestRatingScore(0)
                ->setBestLastTouchDateScore(0)
                ->setBestPlayCountScore(0)
                ->setBestPlayedPerTouchScore(0)
                ->setBestRatingScoreDate(new \DateTime())
                ->setBestLastTouchDateScoreDate(new \DateTime())
                ->setBestPlayCountScoreDate(new \DateTime())
                ->setBestPlayedPerTouchScoreDate(new \DateTime())
            ;

            $workHasSong = new WorkHasSong();
            $workHasSong
                ->setType(WorkHasSong::TYPE_PRIMARY)
                ->setSong($song)
                ->setWork($work)
            ;

            $this->entityManager->persist($work);
            $this->entityManager->persist($workHasSong);

            $this->entityManager->flush();

            $this->entityManager->clear(Song::class);
            $this->entityManager->clear(Work::class);
            $this->entityManager->clear(WorkHasSong::class);
        }

        $progressBar->finish();
        $this->output->writeln('');
    }

    private function getSongsWithoutWork(): array
    {
        $stmt = $this
            ->entityManager
            ->getConnection()
            ->prepare('
                SELECT
                    id
                
                FROM song s 
                
                WHERE
                    NOT EXISTS (
                        SELECT 1
                        
                        FROM work_has_song whs 
                        
                        WHERE
                            whs.song_id = s.id 
                        
                        LIMIT 1
                    )
            ')
        ;

        $stmt->execute();

        return $stmt->fetchAll(FetchMode::COLUMN);
    }
}
