<?php

namespace App;

use Doctrine\DBAL\Connection;

class MediaMonkeyDatabase
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getTables(): array
    {
        $stmt = $this
            ->getConnection()
            ->prepare("
                SELECT * 
                FROM sqlite_master
                WHERE
                    type='table'
            ")
        ;
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getTableNames(): array
    {
        $tables = array_column($this->getTables(), 'name');

        natcasesort($tables);

        return $tables;
    }
}
