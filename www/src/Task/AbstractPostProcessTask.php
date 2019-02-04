<?php

namespace App\Task;

use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractPostProcessTask
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    abstract public function run();

    protected function execSql(
        string $sql,
        array $params = []
    ): Statement {
        $stmt = $this
            ->entityManager
            ->getConnection()
            ->prepare($sql)
        ;
        $stmt->execute($params);

        return $stmt;
    }

    protected function execSqlFetch(
        string $sql,
        array $params,
        int $fetchMode = FetchMode::ASSOCIATIVE
    ) {
        $stmt = $this->execSql($sql, $params);

        return $stmt->fetch($fetchMode);
    }
}
