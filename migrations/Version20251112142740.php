<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112142740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email_confirmation (id VARCHAR(36) NOT NULL, user_id VARCHAR(36) NOT NULL, created_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, confirmed_at DATETIME DEFAULT NULL, token VARCHAR(64) NOT NULL, INDEX IDX_1D2EF46FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_confirmation ADD CONSTRAINT FK_1D2EF46FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_confirmation DROP FOREIGN KEY FK_1D2EF46FA76ED395');
        $this->addSql('DROP TABLE email_confirmation');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
