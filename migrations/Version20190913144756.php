<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190913144756 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_AEDAD51C5E237E06');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('CREATE TABLE exercise (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:uuid)
        , user CHAR(36) DEFAULT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL COLLATE BINARY, attributes CLOB NOT NULL COLLATE BINARY --(DC2Type:json_array)
        , PRIMARY KEY(id), CONSTRAINT FK_AEDAD51C8D93D649 FOREIGN KEY (user) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_AEDAD51C8D93D649 ON exercise (user)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AEDAD51C5E237E068D93D649 ON exercise (name, user)');
        $this->addSql('DROP INDEX IDX_9B349F91A76ED395');
        $this->addSql('DROP INDEX IDX_9B349F91E934951A');
        $this->addSql('DROP TABLE record');
        $this->addSql('CREATE TABLE record (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:uuid)
        , exercise_id CHAR(36) DEFAULT NULL --(DC2Type:uuid)
        , earned_at DATETIME NOT NULL, val CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , PRIMARY KEY(id), CONSTRAINT FK_9B349F91E934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9B349F91E934951A ON record (exercise_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_AEDAD51C8D93D649');
        $this->addSql('DROP INDEX UNIQ_AEDAD51C5E237E068D93D649');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('CREATE TABLE exercise (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, attributes CLOB NOT NULL --(DC2Type:json_array)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AEDAD51C5E237E06 ON exercise (name)');
        $this->addSql('DROP INDEX IDX_9B349F91E934951A');
        $this->addSql('CREATE TABLE record (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , earned_at DATETIME NOT NULL, val CLOB NOT NULL --(DC2Type:json)
        , exercise_id CHAR(36) DEFAULT \'NULL --(DC2Type:uuid)\' COLLATE BINARY --\' COLLATE BINARY --(DC2Type:uuid)
        , user_id CHAR(36) DEFAULT \'NULL --(DC2Type:uuid)\' COLLATE BINARY --\' COLLATE BINARY --(DC2Type:uuid)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9B349F91E934951A ON record (exercise_id)');
        $this->addSql('CREATE INDEX IDX_9B349F91A76ED395 ON record (user_id)');
    }
}
