<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190425221000 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE playback_aggregation (id INT AUTO_INCREMENT NOT NULL, song_id INT NOT NULL, period VARCHAR(255) NOT NULL, count INT NOT NULL, total_count INT NOT NULL, percentage DOUBLE PRECISION NOT NULL, INDEX IDX_A38B9013A0BDB2F3 (song_id), UNIQUE INDEX song_id_period_unique (song_id, period), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE playback_aggregation ADD CONSTRAINT FK_A38B9013A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE playback_aggregation');
    }
}
