<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250615020114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__challenge_exercise AS SELECT id, challenge_id, exercise_id, completed FROM challenge_exercise
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE challenge_exercise
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE challenge_exercise (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, challenge_id INTEGER DEFAULT NULL, exercise_id INTEGER NOT NULL, completed BOOLEAN NOT NULL, CONSTRAINT FK_33E432FC98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_33E432FCE934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO challenge_exercise (id, challenge_id, exercise_id, completed) SELECT id, challenge_id, exercise_id, completed FROM __temp__challenge_exercise
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__challenge_exercise
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_33E432FCE934951A ON challenge_exercise (exercise_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_33E432FC98A21AC6 ON challenge_exercise (challenge_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__challenge_exercise AS SELECT id, challenge_id, exercise_id, completed FROM challenge_exercise
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE challenge_exercise
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE challenge_exercise (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, challenge_id INTEGER NOT NULL, exercise_id INTEGER NOT NULL, completed BOOLEAN NOT NULL, CONSTRAINT FK_33E432FC98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_33E432FCE934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO challenge_exercise (id, challenge_id, exercise_id, completed) SELECT id, challenge_id, exercise_id, completed FROM __temp__challenge_exercise
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__challenge_exercise
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_33E432FC98A21AC6 ON challenge_exercise (challenge_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_33E432FCE934951A ON challenge_exercise (exercise_id)
        SQL);
    }
}
