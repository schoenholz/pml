<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190402201038 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE song_duplicate_proposal (id INT AUTO_INCREMENT NOT NULL, song_a_id INT NOT NULL, song_b_id INT NOT NULL, is_dismissed TINYINT(1) NOT NULL, INDEX IDX_77A8FBF2484D85AD (song_a_id), INDEX IDX_77A8FBF25AF82A43 (song_b_id), UNIQUE INDEX song_a_id_song_b_id_unq (song_a_id, song_b_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE song_duplicate_proposal ADD CONSTRAINT FK_77A8FBF2484D85AD FOREIGN KEY (song_a_id) REFERENCES song (id)');
        $this->addSql('ALTER TABLE song_duplicate_proposal ADD CONSTRAINT FK_77A8FBF25AF82A43 FOREIGN KEY (song_b_id) REFERENCES song (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE song_duplicate_proposal');
    }
}
