<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20190204234506 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, song_id INT NOT NULL, song_relation VARCHAR(255) NOT NULL, file_path_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8C9F3610BD404827 (file_path_name), INDEX IDX_8C9F3610A0BDB2F3 (song_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meta_file (id INT AUTO_INCREMENT NOT NULL, meta_lib_id INT NOT NULL, file_id INT NOT NULL, external_id VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, title_normalized VARCHAR(255) DEFAULT NULL, album VARCHAR(255) DEFAULT NULL, disk_number INT DEFAULT NULL, track_number INT DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, year INT DEFAULT NULL, date DATETIME DEFAULT NULL, bitrate INT DEFAULT NULL, sampling_frequency INT DEFAULT NULL, rating INT DEFAULT NULL, bpm INT DEFAULT NULL, initial_key VARCHAR(255) DEFAULT NULL, added_date DATETIME NOT NULL, first_import_date DATETIME NOT NULL, last_import_date DATETIME NOT NULL, is_deleted TINYINT(1) NOT NULL, deletion_date DATETIME DEFAULT NULL, INDEX IDX_1F39455340A1F7F2 (meta_lib_id), INDEX IDX_1F39455393CB796C (file_id), UNIQUE INDEX meta_lib_file_unq (meta_lib_id, file_id), UNIQUE INDEX meta_lib_external_id_unq (meta_lib_id, external_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meta_file_artist (id INT AUTO_INCREMENT NOT NULL, meta_file_id INT NOT NULL, relation VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, title_normalized VARCHAR(255) NOT NULL, INDEX IDX_118984CFDAB0668E (meta_file_id), UNIQUE INDEX meta_file_relation_title_unq (meta_file_id, relation, title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meta_file_genre (id INT AUTO_INCREMENT NOT NULL, meta_file_id INT NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_89382162DAB0668E (meta_file_id), UNIQUE INDEX meta_file_title_unq (meta_file_id, title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meta_file_rating (id INT AUTO_INCREMENT NOT NULL, meta_file_id INT NOT NULL, rating INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_C859346ADAB0668E (meta_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meta_file_touch (id INT AUTO_INCREMENT NOT NULL, meta_file_id INT NOT NULL, type VARCHAR(255) NOT NULL, date DATETIME NOT NULL, count INT NOT NULL, INDEX IDX_FCDE3F68DAB0668E (meta_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meta_lib (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, root_path VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_683E5415E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song (id INT AUTO_INCREMENT NOT NULL, rating INT DEFAULT NULL, added_date DATETIME NOT NULL, days_in_library INT NOT NULL, touch_count INT NOT NULL, first_touch_date DATETIME DEFAULT NULL, last_touch_date DATETIME DEFAULT NULL, days_between_first_and_last_touch INT DEFAULT NULL, days_since_first_touch INT NOT NULL, days_since_last_touch INT NOT NULL, play_count INT NOT NULL, first_play_date DATETIME DEFAULT NULL, last_play_date DATETIME DEFAULT NULL, played_per_touch_quota DOUBLE PRECISION NOT NULL, played_per_day_between_first_and_last_touch_quota DOUBLE PRECISION NOT NULL, skip_count INT NOT NULL, first_skip_date DATETIME DEFAULT NULL, last_skip_date DATETIME DEFAULT NULL, skipped_per_touch_quota DOUBLE PRECISION NOT NULL, skipped_per_day_between_first_and_last_touch_quota DOUBLE PRECISION NOT NULL, rating_score INT NOT NULL, best_rating_score INT NOT NULL, best_rating_score_date DATETIME NOT NULL, last_touch_date_score INT NOT NULL, best_last_touch_date_score INT NOT NULL, best_last_touch_date_score_date DATETIME NOT NULL, play_count_score INT NOT NULL, best_play_count_score INT NOT NULL, best_play_count_score_date DATETIME NOT NULL, played_per_touch_score INT NOT NULL, best_played_per_touch_score INT NOT NULL, best_played_per_touch_score_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
        $this->addSql('ALTER TABLE meta_file ADD CONSTRAINT FK_1F39455340A1F7F2 FOREIGN KEY (meta_lib_id) REFERENCES meta_lib (id)');
        $this->addSql('ALTER TABLE meta_file ADD CONSTRAINT FK_1F39455393CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE meta_file_artist ADD CONSTRAINT FK_118984CFDAB0668E FOREIGN KEY (meta_file_id) REFERENCES meta_file (id)');
        $this->addSql('ALTER TABLE meta_file_genre ADD CONSTRAINT FK_89382162DAB0668E FOREIGN KEY (meta_file_id) REFERENCES meta_file (id)');
        $this->addSql('ALTER TABLE meta_file_rating ADD CONSTRAINT FK_C859346ADAB0668E FOREIGN KEY (meta_file_id) REFERENCES meta_file (id)');
        $this->addSql('ALTER TABLE meta_file_touch ADD CONSTRAINT FK_FCDE3F68DAB0668E FOREIGN KEY (meta_file_id) REFERENCES meta_file (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE meta_file DROP FOREIGN KEY FK_1F39455393CB796C');
        $this->addSql('ALTER TABLE meta_file_artist DROP FOREIGN KEY FK_118984CFDAB0668E');
        $this->addSql('ALTER TABLE meta_file_genre DROP FOREIGN KEY FK_89382162DAB0668E');
        $this->addSql('ALTER TABLE meta_file_rating DROP FOREIGN KEY FK_C859346ADAB0668E');
        $this->addSql('ALTER TABLE meta_file_touch DROP FOREIGN KEY FK_FCDE3F68DAB0668E');
        $this->addSql('ALTER TABLE meta_file DROP FOREIGN KEY FK_1F39455340A1F7F2');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610A0BDB2F3');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE meta_file');
        $this->addSql('DROP TABLE meta_file_artist');
        $this->addSql('DROP TABLE meta_file_genre');
        $this->addSql('DROP TABLE meta_file_rating');
        $this->addSql('DROP TABLE meta_file_touch');
        $this->addSql('DROP TABLE meta_lib');
        $this->addSql('DROP TABLE song');
    }
}
