<?php

namespace App\Command;

use App\Entity\LastFmPlayback;
use App\LastFm\Api\UserApi;
use App\Repository\LastFmPlaybackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchLastFmPlaybacksCommand extends Command
{
    /**
     * @var UserApi
     */
    private $userApi;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LastFmPlaybackRepository
     */
    private $lastFmPlaybackRepository;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(
        UserApi $userApi,
        EntityManagerInterface $entityManager,
        LastFmPlaybackRepository $lastFmPlaybackRepository
    ) {
        parent::__construct();

        $this->userApi = $userApi;
        $this->entityManager = $entityManager;
        $this->lastFmPlaybackRepository = $lastFmPlaybackRepository;
    }

    protected function configure()
    {
        $this
            ->setName('app:last_fm:fetch_playbacks')
            ->setDescription('Do last.fm stuff.')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->output = $output;

        $fetches = 0;
        $maxFetches = 300;
        $maxFetchDate = new \DateTime('now');
        $precision = new \App\DateInterval('PT1H');
        $user = 't1n3f';
        $fromDate = $this->getFetchFromDate($user);

        while ($fetches < $maxFetches) {
            $fetches ++;
            $toDate = clone $fromDate;
            $toDate->add($precision);

            if ($toDate > $maxFetchDate) {
                break;
            }

            $this->output->writeln(sprintf('Fetching from %s to %s (%d).', $fromDate->format('Y-m-d H:i'), $toDate->format('Y-m-d H:i'), $fetches));

            $res = $this->userApi->getWeeklyTrackChart($user, $fromDate, $toDate)['weeklytrackchart'];
            $reportFromDate = new \DateTime();
            $reportFromDate->setTimestamp($res['@attr']['from']);
            $reportToDate = new \DateTime();
            $reportToDate->setTimestamp($res['@attr']['to']);

            foreach ($res['track'] as $track) {
                $this->output->write('.');
                $lastFmPlayback = new LastFmPlayback();
                $lastFmPlayback
                    ->setUser($res['@attr']['user'])
                    ->setArtistTitle($track['artist']['#text'])
                    ->setArtistMbid($track['artist']['mbid'])
                    ->setTrackTitle($track['name'])
                    ->setTrackMbid($track['mbid'])
                    ->setCount($track['playcount'])
                    ->setDate($reportToDate)
                    ->setPrec($reportToDate->getTimestamp() - $reportFromDate->getTimestamp())
                ;

                $this->entityManager->persist($lastFmPlayback);
                $this->entityManager->flush();

                $this->entityManager->clear(LastFmPlayback::class);
            }

            if (!empty($res['track'])) {
                $this->output->writeln('');
            }

            $fromDate->add($precision);
        }
    }

    private function getFetchFromDate(string $user): \DateTime
    {
        $maxPlaybackDate = $this->lastFmPlaybackRepository->getMaxPlayDate($user);

        if (!$maxPlaybackDate) {
            return new \DateTime('2018-07-29 00:00:00');
        }

        // Integrity check.
        $integrityCheckPrecision = new \DateInterval('P3D');
        $checkTo = clone $maxPlaybackDate;
        $checkFrom = clone $checkTo;
        $checkFrom->sub($integrityCheckPrecision);

        $this->output->writeln(sprintf('Checking play count between %s and %s...', $checkFrom->format('Y-m-d H:i:s'), $checkTo->format('Y-m-d H:i:s')));

        $localCount = $this->lastFmPlaybackRepository->getPlayCount($user, $checkFrom, $checkTo);
        $res = $this->userApi->getWeeklyTrackChart($user, $checkFrom, $checkTo)['weeklytrackchart'];

        $remoteCount = 0;
        foreach ($res['track'] as $track) {
            $remoteCount += $track['playcount'];
        }

        if ($remoteCount == $localCount) {
            return $maxPlaybackDate;
        }

        $this->output->writeln(sprintf(
            'Clearing playbacks after %s: local play count %d does not match remote count %d',
            $checkFrom->format('Y-m-d H:i:s'),
            $localCount,
            $remoteCount
        ));

        // Remote count does not match local count.
        $this
            ->lastFmPlaybackRepository
            ->createQueryBuilder('lfp')
            ->delete()
            ->where('lfp.user = :user')
            ->setParameter(':user', $user)
            ->andWhere('lfp.date >= :date')
            ->setParameter(':date', $checkFrom)
            ->getQuery()
            ->execute()
        ;

        return $this->getFetchFromDate($user);
    }
}
