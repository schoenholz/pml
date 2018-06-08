<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20180603170422 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE library (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A18098BC5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE library_file (id INT AUTO_INCREMENT NOT NULL, library_id INT NOT NULL, path VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_file_exists TINYINT(1) NOT NULL, file_created_at DATETIME NOT NULL, file_modified_at DATETIME NOT NULL, file_size INT NOT NULL, analyzed_at DATETIME DEFAULT NULL, scanned_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2A50FC7DFE2541D7 (library_id), UNIQUE INDEX path_name_unq (path, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE library_file_attribute (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, is_static TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_FA78E5545E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE library_file_attribute_value (id INT AUTO_INCREMENT NOT NULL, library_file_id INT NOT NULL, library_file_attribute_id INT NOT NULL, value_bool TINYINT(1) DEFAULT NULL, value_date DATE DEFAULT NULL, value_date_time DATETIME DEFAULT NULL, value_float NUMERIC(30, 15) DEFAULT NULL, value_int INT DEFAULT NULL, value_string VARCHAR(4096) DEFAULT NULL, INDEX IDX_F6BCC893E90AC15C (library_file_id), INDEX IDX_F6BCC8937023BD01 (library_file_attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D782112D5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist_entry (id INT AUTO_INCREMENT NOT NULL, playlist_id INT NOT NULL, library_file_id INT NOT NULL, state INT NOT NULL, position INT NOT NULL, INDEX IDX_883D84336BBD148 (playlist_id), INDEX IDX_883D8433E90AC15C (library_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE library_file ADD CONSTRAINT FK_2A50FC7DFE2541D7 FOREIGN KEY (library_id) REFERENCES library (id)');
        $this->addSql('ALTER TABLE library_file_attribute_value ADD CONSTRAINT FK_F6BCC893E90AC15C FOREIGN KEY (library_file_id) REFERENCES library_file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE library_file_attribute_value ADD CONSTRAINT FK_F6BCC8937023BD01 FOREIGN KEY (library_file_attribute_id) REFERENCES library_file_attribute (id)');
        $this->addSql('ALTER TABLE playlist_entry ADD CONSTRAINT FK_883D84336BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id)');
        $this->addSql('ALTER TABLE playlist_entry ADD CONSTRAINT FK_883D8433E90AC15C FOREIGN KEY (library_file_id) REFERENCES library_file (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE library_file DROP FOREIGN KEY FK_2A50FC7DFE2541D7');
        $this->addSql('ALTER TABLE library_file_attribute_value DROP FOREIGN KEY FK_F6BCC893E90AC15C');
        $this->addSql('ALTER TABLE playlist_entry DROP FOREIGN KEY FK_883D8433E90AC15C');
        $this->addSql('ALTER TABLE library_file_attribute_value DROP FOREIGN KEY FK_F6BCC8937023BD01');
        $this->addSql('ALTER TABLE playlist_entry DROP FOREIGN KEY FK_883D84336BBD148');
        $this->addSql('DROP TABLE library');
        $this->addSql('DROP TABLE library_file');
        $this->addSql('DROP TABLE library_file_attribute');
        $this->addSql('DROP TABLE library_file_attribute_value');
        $this->addSql('DROP TABLE playlist');
        $this->addSql('DROP TABLE playlist_entry');
    }
}
