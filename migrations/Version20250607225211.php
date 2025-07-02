<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250607225211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `primary` ON participant');
        $this->addSql('ALTER TABLE participant ADD id VARCHAR(36) NOT NULL, CHANGE conversation_id conversation_id VARCHAR(36) DEFAULT NULL, CHANGE user_id user_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `PRIMARY` ON participant');
        $this->addSql('ALTER TABLE participant DROP id, CHANGE conversation_id conversation_id VARCHAR(36) NOT NULL, CHANGE user_id user_id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE participant ADD PRIMARY KEY (conversation_id, user_id)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
