<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614134625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__challenge AS SELECT id, user_id, started_at, completed_at FROM challenge
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE challenge
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE challenge (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, started_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , completed_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_D7098951A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO challenge (id, user_id, started_at, completed_at) SELECT id, user_id, started_at, completed_at FROM __temp__challenge
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__challenge
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D7098951A76ED395 ON challenge (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__exercise AS SELECT id, challenge_id, name, description, muscle_group FROM exercise
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE exercise
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE exercise (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, challenge_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, muscle_group VARCHAR(255) NOT NULL, CONSTRAINT FK_AEDAD51C98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO exercise (id, challenge_id, name, description, muscle_group) SELECT id, challenge_id, name, description, muscle_group FROM __temp__exercise
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__exercise
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AEDAD51C98A21AC6 ON exercise (challenge_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__challenge AS SELECT id, user_id, started_at, completed_at FROM challenge
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE challenge
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE challenge (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, started_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , completed_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO challenge (id, user_id, started_at, completed_at) SELECT id, user_id, started_at, completed_at FROM __temp__challenge
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__challenge
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__exercise AS SELECT id, challenge_id, name, description, muscle_group FROM exercise
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE exercise
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE exercise (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, challenge_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, muscle_group VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO exercise (id, challenge_id, name, description, muscle_group) SELECT id, challenge_id, name, description, muscle_group FROM __temp__exercise
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__exercise
        SQL);
    }
}
