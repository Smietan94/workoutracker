<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230802133536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sets DROP FOREIGN KEY FK_948D45D1B91BE1B6');
        $this->addSql('DROP INDEX IDX_948D45D1B91BE1B6 ON sets');
        $this->addSql('ALTER TABLE sets CHANGE execise_id exercise_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE sets ADD CONSTRAINT FK_948D45D1E934951A FOREIGN KEY (exercise_id) REFERENCES exercises (id)');
        $this->addSql('CREATE INDEX IDX_948D45D1E934951A ON sets (exercise_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sets DROP FOREIGN KEY FK_948D45D1E934951A');
        $this->addSql('DROP INDEX IDX_948D45D1E934951A ON sets');
        $this->addSql('ALTER TABLE sets CHANGE exercise_id execise_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE sets ADD CONSTRAINT FK_948D45D1B91BE1B6 FOREIGN KEY (execise_id) REFERENCES exercises (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_948D45D1B91BE1B6 ON sets (execise_id)');
    }
}
