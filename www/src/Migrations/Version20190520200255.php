<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190520200255 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE song DROP days_between_first_and_last_touch, DROP days_since_first_touch, DROP days_since_last_touch, DROP skipped_per_touch_quota, DROP best_last_touch_date_score, DROP best_last_touch_date_score_date, DROP best_played_per_touch_score, DROP best_played_per_touch_score_date');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE song ADD days_between_first_and_last_touch INT DEFAULT NULL, ADD days_since_first_touch INT DEFAULT NULL, ADD days_since_last_touch INT DEFAULT NULL, ADD skipped_per_touch_quota DOUBLE PRECISION NOT NULL, ADD best_last_touch_date_score INT NOT NULL, ADD best_last_touch_date_score_date DATETIME NOT NULL, ADD best_played_per_touch_score INT NOT NULL, ADD best_played_per_touch_score_date DATETIME NOT NULL');
    }
}
