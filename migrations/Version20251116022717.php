<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251116022717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team CHANGE creator_id creator_id VARCHAR(36) NOT NULL, CHANGE leader_id leader_id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE team_request ADD accepted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team CHANGE creator_id creator_id VARCHAR(36) DEFAULT NULL, CHANGE leader_id leader_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE team_request DROP accepted_at');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
