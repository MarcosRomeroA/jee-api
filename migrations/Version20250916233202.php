<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916233202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification ADD message_id VARCHAR(36) DEFAULT NULL, CHANGE notification_type_id notification_type_id VARCHAR(36) DEFAULT NULL, CHANGE user_id user_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA537A1329 ON notification (message_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA537A1329');
        $this->addSql('DROP INDEX IDX_BF5476CA537A1329 ON notification');
        $this->addSql('ALTER TABLE notification DROP message_id, CHANGE notification_type_id notification_type_id VARCHAR(36) NOT NULL, CHANGE user_id user_id VARCHAR(36) NOT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
