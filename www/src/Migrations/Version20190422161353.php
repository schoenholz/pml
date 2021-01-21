<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190422161353 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D782112D5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist_item (id INT AUTO_INCREMENT NOT NULL, playlist_id INT NOT NULL, file_id INT NOT NULL, position INT NOT NULL, INDEX IDX_BF02127C6BBD148 (playlist_id), INDEX IDX_BF02127C93CB796C (file_id), UNIQUE INDEX playlist_position_unique (playlist_id, position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE playlist_item ADD CONSTRAINT FK_BF02127C6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id)');
        $this->addSql('ALTER TABLE playlist_item ADD CONSTRAINT FK_BF02127C93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE playlist_item DROP FOREIGN KEY FK_BF02127C6BBD148');
        $this->addSql('DROP TABLE playlist');
        $this->addSql('DROP TABLE playlist_item');
    }
}
