<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190428212511 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE playback_aggregation DROP FOREIGN KEY FK_A38B9013A0BDB2F3');
        $this->addSql('ALTER TABLE playback_aggregation ADD CONSTRAINT FK_A38B9013A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE playback_aggregation DROP FOREIGN KEY FK_A38B9013A0BDB2F3');
        $this->addSql('ALTER TABLE playback_aggregation ADD CONSTRAINT FK_A38B9013A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
    }
}
