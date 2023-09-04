<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230904110629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exercise_results (id INT UNSIGNED AUTO_INCREMENT NOT NULL, exercise_id INT UNSIGNED DEFAULT NULL, weight NUMERIC(8, 2) NOT NULL, notes VARCHAR(255) NOT NULL, date DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_45B61351E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exercise_results ADD CONSTRAINT FK_45B61351E934951A FOREIGN KEY (exercise_id) REFERENCES exercises (id)');
        $this->addSql('ALTER TABLE ecercise_results DROP FOREIGN KEY FK_DF202AE0E934951A');
        $this->addSql('DROP TABLE ecercise_results');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ecercise_results (id INT UNSIGNED AUTO_INCREMENT NOT NULL, exercise_id INT UNSIGNED DEFAULT NULL, weight NUMERIC(8, 2) NOT NULL, notes VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, date DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DF202AE0E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ecercise_results ADD CONSTRAINT FK_DF202AE0E934951A FOREIGN KEY (exercise_id) REFERENCES exercises (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE exercise_results DROP FOREIGN KEY FK_45B61351E934951A');
        $this->addSql('DROP TABLE exercise_results');
    }
}
