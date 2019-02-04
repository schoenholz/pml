<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181218234520 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE song DROP days_between_first_and_last_touch, DROP days_since_first_touch, DROP days_since_last_touch, DROP played_per_touch, DROP played_per_day_since_first_touch, DROP played_per_day_from_first_to_last_touch, DROP skipped_per_touch, DROP skipped_per_day_since_first_touch, DROP skipped_per_day_from_first_to_last_touch, DROP days_in_library, DROP rating_score, DROP last_touch_date_score, DROP play_count_score, DROP played_per_touch_score');
        $this->addSql('ALTER TABLE work ADD added_date DATETIME NOT NULL, ADD days_in_library INT NOT NULL, ADD played_per_day_between_first_and_last_touch_quota DOUBLE PRECISION NOT NULL, ADD skipped_per_day_between_first_and_last_touch_quota DOUBLE PRECISION NOT NULL, ADD rating_score INT NOT NULL, ADD best_rating_score INT NOT NULL, ADD best_rating_score_date DATETIME NOT NULL, ADD last_touch_date_score INT NOT NULL, ADD best_last_touch_date_rating_score INT NOT NULL, ADD best_last_touch_date_rating_score_date DATETIME NOT NULL, ADD play_count_score INT NOT NULL, ADD best_play_count_score INT NOT NULL, ADD best_play_count_score_date INT NOT NULL, ADD played_per_touch_score INT NOT NULL, ADD best_played_per_touch_score INT NOT NULL, ADD best_played_per_touch_score_date DATETIME NOT NULL, DROP title');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE song ADD days_between_first_and_last_touch INT NOT NULL, ADD days_since_first_touch INT DEFAULT NULL, ADD days_since_last_touch INT NOT NULL, ADD played_per_touch DOUBLE PRECISION NOT NULL, ADD played_per_day_since_first_touch DOUBLE PRECISION NOT NULL, ADD played_per_day_from_first_to_last_touch DOUBLE PRECISION NOT NULL, ADD skipped_per_touch DOUBLE PRECISION NOT NULL, ADD skipped_per_day_since_first_touch DOUBLE PRECISION NOT NULL, ADD skipped_per_day_from_first_to_last_touch DOUBLE PRECISION NOT NULL, ADD days_in_library INT NOT NULL, ADD rating_score INT NOT NULL, ADD last_touch_date_score INT NOT NULL, ADD play_count_score INT NOT NULL, ADD played_per_touch_score INT NOT NULL');
        $this->addSql('ALTER TABLE work ADD title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP added_date, DROP days_in_library, DROP played_per_day_between_first_and_last_touch_quota, DROP skipped_per_day_between_first_and_last_touch_quota, DROP rating_score, DROP best_rating_score, DROP best_rating_score_date, DROP last_touch_date_score, DROP best_last_touch_date_rating_score, DROP best_last_touch_date_rating_score_date, DROP play_count_score, DROP best_play_count_score, DROP best_play_count_score_date, DROP played_per_touch_score, DROP best_played_per_touch_score, DROP best_played_per_touch_score_date');
    }
}
