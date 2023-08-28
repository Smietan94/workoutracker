<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230828184028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exercises ADD user_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE exercises ADD CONSTRAINT FK_FA14991A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_FA14991A76ED395 ON exercises (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exercises DROP FOREIGN KEY FK_FA14991A76ED395');
        $this->addSql('DROP INDEX IDX_FA14991A76ED395 ON exercises');
        $this->addSql('ALTER TABLE exercises DROP user_id');
    }
}
