<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181216205035 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE work (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, touch_count INT NOT NULL, first_touch_date DATETIME DEFAULT NULL, last_touch_date DATETIME DEFAULT NULL, days_between_first_and_last_touch INT DEFAULT NULL, days_since_first_touch INT NOT NULL, days_since_last_touch INT NOT NULL, play_count INT NOT NULL, first_play_date DATETIME DEFAULT NULL, last_play_date DATETIME DEFAULT NULL, played_per_touch_quota DOUBLE PRECISION NOT NULL, skip_count INT NOT NULL, first_skip_date DATETIME DEFAULT NULL, last_skip_date DATETIME DEFAULT NULL, skipped_per_touch_quota DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_has_song (id INT AUTO_INCREMENT NOT NULL, work_id INT NOT NULL, song_id INT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_BB04BFA2BB3453DB (work_id), INDEX IDX_BB04BFA2A0BDB2F3 (song_id), UNIQUE INDEX work_song_unq (work_id, song_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE work_has_song ADD CONSTRAINT FK_BB04BFA2BB3453DB FOREIGN KEY (work_id) REFERENCES work (id)');
        $this->addSql('ALTER TABLE work_has_song ADD CONSTRAINT FK_BB04BFA2A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE work_has_song DROP FOREIGN KEY FK_BB04BFA2BB3453DB');
        $this->addSql('DROP TABLE work');
        $this->addSql('DROP TABLE work_has_song');
    }
}
