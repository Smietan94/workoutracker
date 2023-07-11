<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230623200327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exercises ADD category_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE exercises ADD CONSTRAINT FK_FA1499112469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_FA1499112469DE2 ON exercises (category_id)');
        $this->addSql('ALTER TABLE workout_plans DROP FOREIGN KEY FK_6CAC2BC512469DE2');
        $this->addSql('DROP INDEX IDX_6CAC2BC512469DE2 ON workout_plans');
        $this->addSql('ALTER TABLE workout_plans DROP category_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workout_plans ADD category_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE workout_plans ADD CONSTRAINT FK_6CAC2BC512469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6CAC2BC512469DE2 ON workout_plans (category_id)');
        $this->addSql('ALTER TABLE exercises DROP FOREIGN KEY FK_FA1499112469DE2');
        $this->addSql('DROP INDEX IDX_FA1499112469DE2 ON exercises');
        $this->addSql('ALTER TABLE exercises DROP category_id');
    }
}
