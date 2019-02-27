<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190214212633 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flat_song DROP FOREIGN KEY FK_5BAC385593CB796C');
        $this->addSql('ALTER TABLE flat_song DROP FOREIGN KEY FK_5BAC3855A0BDB2F3');
        $this->addSql('ALTER TABLE flat_song ADD CONSTRAINT FK_5BAC385593CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flat_song ADD CONSTRAINT FK_5BAC3855A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flat_song DROP FOREIGN KEY FK_5BAC3855A0BDB2F3');
        $this->addSql('ALTER TABLE flat_song DROP FOREIGN KEY FK_5BAC385593CB796C');
        $this->addSql('ALTER TABLE flat_song ADD CONSTRAINT FK_5BAC3855A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
        $this->addSql('ALTER TABLE flat_song ADD CONSTRAINT FK_5BAC385593CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
    }
}
