<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190213191319 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE flat_song (id INT AUTO_INCREMENT NOT NULL, song_id INT NOT NULL, file_id INT NOT NULL, artist VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, file_is_synthetic TINYINT(1) NOT NULL, INDEX IDX_5BAC3855A0BDB2F3 (song_id), INDEX IDX_5BAC385593CB796C (file_id), UNIQUE INDEX song_file_artist_title_unq (song_id, file_id, artist, title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE flat_song ADD CONSTRAINT FK_5BAC3855A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
        $this->addSql('ALTER TABLE flat_song ADD CONSTRAINT FK_5BAC385593CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE flat_song');
    }
}
