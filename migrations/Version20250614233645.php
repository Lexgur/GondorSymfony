<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614233645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE challenge_exercise (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, challenge_id INTEGER NOT NULL, exercise_id INTEGER NOT NULL, completed BOOLEAN NOT NULL, CONSTRAINT FK_33E432FC98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_33E432FCE934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_33E432FC98A21AC6 ON challenge_exercise (challenge_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_33E432FCE934951A ON challenge_exercise (exercise_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__exercise AS SELECT id, challenge_id, name, description, muscle_group FROM exercise
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE exercise
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE exercise (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, challenge_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, muscle_group VARCHAR(255) NOT NULL, CONSTRAINT FK_AEDAD51C98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
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
            DROP TABLE challenge_exercise
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exercise ADD COLUMN completed BOOLEAN DEFAULT 0 NOT NULL
        SQL);
    }
}
