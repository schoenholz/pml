<?php

namespace App\Command;

use App\Console\Helper\BarChart;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatsAddedCommand extends Command
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
            ->setName('app:stats:added')
            ->setDescription('Statistics: added per month.')
            ->addArgument('min_year', InputArgument::OPTIONAL, '', null)
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $stmt = $this
            ->entityManager
            ->getConnection()
            ->prepare('SELECT MIN(YEAR(added_date)) FROM song')
        ;
        $stmt->execute();

        $startYearFromDb = $stmt->fetch(FetchMode::COLUMN);
        if (!$startYearFromDb) {
            $startYearFromDb = date('Y');
        }

        $startYearFromArgument = $input->getArgument('min_year');
        if ($startYearFromArgument === null) {
            $startYear = (int) $startYearFromDb;
        } else {
            $startYear = (int) $startYearFromArgument;
        }

        $startTime = $startYear * 12 + 1;
        $endTime = date('Y') * 12 + date('m');

        //var_dump($startTime);
        //var_dump($endTime);
        //die;

        $stmt = $this
            ->entityManager
            ->getConnection()
            ->prepare('
                SELECT
                    DATE_FORMAT(added_date, "%Y") * 12 + DATE_FORMAT(added_date, "%m") AS time, 
                    COUNT(1)
                
                FROM song
                
                WHERE
                    YEAR(added_date) >= :year
                
                GROUP BY
                    time ASC
            ')
        ;
        $stmt->execute([
            'year' => $startYear,
        ]);
        $rows = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        $renderRows = [];
        $time = $startTime;
        while ($time <= $endTime) {
            $year = floor(($time - 1) / 12);
            $renderRows[] = [
                $year,
                $time - $year * 12,
                $rows[$time] ?? 0
            ];

            $time ++;
        }

        $barChart = new BarChart(count($renderRows) > 0 ? max(array_column($renderRows, 2)) : 0);
        $renderRows = array_map(function (array $row) use ($barChart) {
            $row[] = $barChart->draw($row[2]);

            return $row;
        }, $renderRows);

        $table = new Table($output);
        $table
            ->setHeaders([
                'Year',
                'Month',
                'Tracks added',
                'Chart',
            ])
            ->setRows($renderRows)
        ;

        $table->render();
    }
}
