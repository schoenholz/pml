<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190402205913 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE song_duplicate_proposal DROP FOREIGN KEY FK_77A8FBF2484D85AD');
        $this->addSql('ALTER TABLE song_duplicate_proposal DROP FOREIGN KEY FK_77A8FBF25AF82A43');
        $this->addSql('ALTER TABLE song_duplicate_proposal ADD CONSTRAINT FK_77A8FBF2484D85AD FOREIGN KEY (song_a_id) REFERENCES song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_duplicate_proposal ADD CONSTRAINT FK_77A8FBF25AF82A43 FOREIGN KEY (song_b_id) REFERENCES song (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE song_duplicate_proposal DROP FOREIGN KEY FK_77A8FBF2484D85AD');
        $this->addSql('ALTER TABLE song_duplicate_proposal DROP FOREIGN KEY FK_77A8FBF25AF82A43');
        $this->addSql('ALTER TABLE song_duplicate_proposal ADD CONSTRAINT FK_77A8FBF2484D85AD FOREIGN KEY (song_a_id) REFERENCES song (id)');
        $this->addSql('ALTER TABLE song_duplicate_proposal ADD CONSTRAINT FK_77A8FBF25AF82A43 FOREIGN KEY (song_b_id) REFERENCES song (id)');
    }
}
