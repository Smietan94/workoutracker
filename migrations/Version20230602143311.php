<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230602143311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3AF34668A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ecercise_results (id INT UNSIGNED AUTO_INCREMENT NOT NULL, exercise_id INT UNSIGNED DEFAULT NULL, weight NUMERIC(8, 2) NOT NULL, notes VARCHAR(255) NOT NULL, date DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DF202AE0E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercises (id INT UNSIGNED AUTO_INCREMENT NOT NULL, exercise_name VARCHAR(255) NOT NULL, sets_number INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, trainingDay_id INT UNSIGNED DEFAULT NULL, INDEX IDX_FA149916B8C22E1 (trainingDay_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sets (id INT UNSIGNED AUTO_INCREMENT NOT NULL, execise_id INT UNSIGNED DEFAULT NULL, set_number INT NOT NULL, reps INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_948D45D1B91BE1B6 (execise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_day_results (id INT UNSIGNED AUTO_INCREMENT NOT NULL, notes VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, trainingDay_id INT UNSIGNED DEFAULT NULL, INDEX IDX_C30DEE416B8C22E1 (trainingDay_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_days (id INT UNSIGNED AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, workoutPlan_id INT UNSIGNED DEFAULT NULL, INDEX IDX_E47EBB25C494C34A (workoutPlan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workout_plans (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, category_id INT UNSIGNED DEFAULT NULL, trainnings INT NOT NULL, notes VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6CAC2BC5A76ED395 (user_id), INDEX IDX_6CAC2BC512469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE ecercise_results ADD CONSTRAINT FK_DF202AE0E934951A FOREIGN KEY (exercise_id) REFERENCES exercises (id)');
        $this->addSql('ALTER TABLE exercises ADD CONSTRAINT FK_FA149916B8C22E1 FOREIGN KEY (trainingDay_id) REFERENCES training_days (id)');
        $this->addSql('ALTER TABLE sets ADD CONSTRAINT FK_948D45D1B91BE1B6 FOREIGN KEY (execise_id) REFERENCES exercises (id)');
        $this->addSql('ALTER TABLE training_day_results ADD CONSTRAINT FK_C30DEE416B8C22E1 FOREIGN KEY (trainingDay_id) REFERENCES training_days (id)');
        $this->addSql('ALTER TABLE training_days ADD CONSTRAINT FK_E47EBB25C494C34A FOREIGN KEY (workoutPlan_id) REFERENCES workout_plans (id)');
        $this->addSql('ALTER TABLE workout_plans ADD CONSTRAINT FK_6CAC2BC5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE workout_plans ADD CONSTRAINT FK_6CAC2BC512469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668A76ED395');
        $this->addSql('ALTER TABLE ecercise_results DROP FOREIGN KEY FK_DF202AE0E934951A');
        $this->addSql('ALTER TABLE exercises DROP FOREIGN KEY FK_FA149916B8C22E1');
        $this->addSql('ALTER TABLE sets DROP FOREIGN KEY FK_948D45D1B91BE1B6');
        $this->addSql('ALTER TABLE training_day_results DROP FOREIGN KEY FK_C30DEE416B8C22E1');
        $this->addSql('ALTER TABLE training_days DROP FOREIGN KEY FK_E47EBB25C494C34A');
        $this->addSql('ALTER TABLE workout_plans DROP FOREIGN KEY FK_6CAC2BC5A76ED395');
        $this->addSql('ALTER TABLE workout_plans DROP FOREIGN KEY FK_6CAC2BC512469DE2');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE ecercise_results');
        $this->addSql('DROP TABLE exercises');
        $this->addSql('DROP TABLE sets');
        $this->addSql('DROP TABLE training_day_results');
        $this->addSql('DROP TABLE training_days');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE workout_plans');
    }
}
