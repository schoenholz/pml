<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190503214118 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE song DROP played_per_day_between_first_and_last_touch_quota, DROP skipped_per_day_between_first_and_last_touch_quota');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE song ADD played_per_day_between_first_and_last_touch_quota DOUBLE PRECISION NOT NULL, ADD skipped_per_day_between_first_and_last_touch_quota DOUBLE PRECISION NOT NULL');
    }
}
