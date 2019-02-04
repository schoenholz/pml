<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181213175520 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE media_monkey_song (id INT AUTO_INCREMENT NOT NULL, media_monkey_id INT NOT NULL, file_path_name VARCHAR(1024) NOT NULL, artist VARCHAR(1024) DEFAULT NULL, title VARCHAR(1024) DEFAULT NULL, publisher VARCHAR(1024) DEFAULT NULL, album VARCHAR(1024) DEFAULT NULL, disc_number INT DEFAULT NULL, track_number INT DEFAULT NULL, year INT DEFAULT NULL, date DATE DEFAULT NULL, play_count INT NOT NULL, skip_count INT NOT NULL, last_played_date DATETIME DEFAULT NULL, first_played_date DATETIME DEFAULT NULL, rating INT DEFAULT NULL, bpm INT DEFAULT NULL, initial_key VARCHAR(32) DEFAULT NULL, added_date DATETIME NOT NULL, bitrate INT DEFAULT NULL, sampling_frequency INT DEFAULT NULL, genre VARCHAR(1024) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song (id INT AUTO_INCREMENT NOT NULL, media_monkey_id INT DEFAULT NULL, file_path_name VARCHAR(1024) NOT NULL, title VARCHAR(255) DEFAULT NULL, album VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, year INT DEFAULT NULL, date DATE DEFAULT NULL, bitrate INT DEFAULT NULL, sampling_frequency INT DEFAULT NULL, rating INT DEFAULT NULL, rating_date DATETIME DEFAULT NULL, best_rating INT DEFAULT NULL, bpm INT DEFAULT NULL, initial_key VARCHAR(255) DEFAULT NULL, disc_number INT DEFAULT NULL, track_number INT DEFAULT NULL, touch_count INT NOT NULL, first_touch_date DATETIME DEFAULT NULL, last_touch_date DATETIME DEFAULT NULL, days_between_first_and_last_touch INT NOT NULL, days_since_first_touch INT DEFAULT NULL, days_since_last_touch INT NOT NULL, play_count INT NOT NULL, first_played_date DATETIME DEFAULT NULL, last_played_date DATETIME DEFAULT NULL, played_per_touch DOUBLE PRECISION NOT NULL, played_per_day_since_first_touch DOUBLE PRECISION NOT NULL, played_per_day_from_first_to_last_touch DOUBLE PRECISION NOT NULL, skip_count INT NOT NULL, first_skipped_date DATETIME DEFAULT NULL, last_skipped_date DATETIME DEFAULT NULL, skipped_per_touch DOUBLE PRECISION NOT NULL, skipped_per_day_since_first_touch DOUBLE PRECISION NOT NULL, skipped_per_day_from_first_to_last_touch DOUBLE PRECISION NOT NULL, added_date DATETIME NOT NULL, days_in_library INT NOT NULL, first_import_date DATETIME NOT NULL, last_import_date DATETIME NOT NULL, is_deleted TINYINT(1) NOT NULL, deletion_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song_artist (id INT AUTO_INCREMENT NOT NULL, song_id INT NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_722870DA0BDB2F3 (song_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song_genre (id INT AUTO_INCREMENT NOT NULL, song_id INT NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_4EF4A6BDA0BDB2F3 (song_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE song_artist ADD CONSTRAINT FK_722870DA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
        $this->addSql('ALTER TABLE song_genre ADD CONSTRAINT FK_4EF4A6BDA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE song_artist DROP FOREIGN KEY FK_722870DA0BDB2F3');
        $this->addSql('ALTER TABLE song_genre DROP FOREIGN KEY FK_4EF4A6BDA0BDB2F3');
        $this->addSql('DROP TABLE media_monkey_song');
        $this->addSql('DROP TABLE song');
        $this->addSql('DROP TABLE song_artist');
        $this->addSql('DROP TABLE song_genre');
    }
}
