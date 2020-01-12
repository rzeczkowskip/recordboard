<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200112101024 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_AEDAD51C5E237E068D93D649');
        $this->addSql('DROP INDEX IDX_AEDAD51C8D93D649');
        $this->addSql('CREATE TEMPORARY TABLE __temp__exercise AS SELECT id, user, name, attributes FROM exercise');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('CREATE TABLE exercise (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , name VARCHAR(255) NOT NULL COLLATE BINARY, attributes CLOB NOT NULL COLLATE BINARY --(DC2Type:json_array)
        , user CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO exercise (id, user, name, attributes) SELECT id, user, name, attributes FROM __temp__exercise');
        $this->addSql('DROP TABLE __temp__exercise');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AEDAD51C5E237E068D93D649 ON exercise (name, user)');
        $this->addSql('DROP INDEX IDX_9B349F91E934951A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__record AS SELECT id, exercise_id, earned_at, val FROM record');
        $this->addSql('DROP TABLE record');
        $this->addSql('CREATE TABLE record (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , earned_at DATETIME NOT NULL, val CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , exercise_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO record (id, exercise_id, earned_at, val) SELECT id, exercise_id, earned_at, val FROM __temp__record');
        $this->addSql('DROP TABLE __temp__record');
        $this->addSql('DROP INDEX IDX_7B42780FA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_api_token AS SELECT token, user_id, generated_at, expires_at FROM user_api_token');
        $this->addSql('DROP TABLE user_api_token');
        $this->addSql('CREATE TABLE user_api_token (token VARCHAR(255) NOT NULL, generated_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , PRIMARY KEY(token))');
        $this->addSql('INSERT INTO user_api_token (token, user_id, generated_at, expires_at) SELECT token, user_id, generated_at, expires_at FROM __temp__user_api_token');
        $this->addSql('DROP TABLE __temp__user_api_token');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_AEDAD51C5E237E068D93D649');
        $this->addSql('CREATE TEMPORARY TABLE __temp__exercise AS SELECT id, name, attributes, user FROM exercise');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('CREATE TABLE exercise (id CHAR(36) NOT NULL --(DC2Type:guid)
        , name VARCHAR(255) NOT NULL, attributes CLOB NOT NULL --(DC2Type:json_array)
        , user CHAR(36) DEFAULT \'NULL --(DC2Type:uuid)\' COLLATE BINARY --(DC2Type:guid)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO exercise (id, name, attributes, user) SELECT id, name, attributes, user FROM __temp__exercise');
        $this->addSql('DROP TABLE __temp__exercise');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AEDAD51C5E237E068D93D649 ON exercise (name, user)');
        $this->addSql('CREATE INDEX IDX_AEDAD51C8D93D649 ON exercise (user)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__record AS SELECT id, earned_at, val, exercise_id FROM record');
        $this->addSql('DROP TABLE record');
        $this->addSql('CREATE TABLE record (id CHAR(36) NOT NULL --(DC2Type:guid)
        , earned_at DATETIME NOT NULL, val CLOB NOT NULL --(DC2Type:json)
        , exercise_id CHAR(36) DEFAULT \'NULL --(DC2Type:uuid)\' COLLATE BINARY --(DC2Type:guid)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO record (id, earned_at, val, exercise_id) SELECT id, earned_at, val, exercise_id FROM __temp__record');
        $this->addSql('DROP TABLE __temp__record');
        $this->addSql('CREATE INDEX IDX_9B349F91E934951A ON record (exercise_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_api_token AS SELECT token, generated_at, expires_at, user_id FROM user_api_token');
        $this->addSql('DROP TABLE user_api_token');
        $this->addSql('CREATE TABLE user_api_token (token CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , generated_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id CHAR(36) DEFAULT \'NULL --(DC2Type:uuid)\' COLLATE BINARY --(DC2Type:guid)
        , PRIMARY KEY(token))');
        $this->addSql('INSERT INTO user_api_token (token, generated_at, expires_at, user_id) SELECT token, generated_at, expires_at, user_id FROM __temp__user_api_token');
        $this->addSql('DROP TABLE __temp__user_api_token');
        $this->addSql('CREATE INDEX IDX_7B42780FA76ED395 ON user_api_token (user_id)');
    }
}
