<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190411202039 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE meta_file CHANGE added_date added_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE song CHANGE added_date added_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE meta_file CHANGE added_date added_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE song CHANGE added_date added_date DATETIME NOT NULL');
    }
}
