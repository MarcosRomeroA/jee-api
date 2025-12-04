<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204154916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id VARCHAR(36) NOT NULL, game_id VARCHAR(36) DEFAULT NULL, type VARCHAR(20) NOT NULL, start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_3BAE0AA7E48FD905 (game_id), INDEX IDX_EVENT_START_AT (start_at), INDEX IDX_EVENT_TYPE (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE post_mention CHANGE id id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE post_mention RENAME INDEX idx_post_mention_post_id TO IDX_DFEAEEFE4B89032C');
        $this->addSql('ALTER TABLE post_mention RENAME INDEX idx_post_mention_user_id TO IDX_DFEAEEFEA76ED395');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7E48FD905');
        $this->addSql('DROP TABLE event');
        $this->addSql('ALTER TABLE post_mention CHANGE id id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE post_mention RENAME INDEX idx_dfeaeefe4b89032c TO IDX_post_mention_post_id');
        $this->addSql('ALTER TABLE post_mention RENAME INDEX idx_dfeaeefea76ed395 TO IDX_post_mention_user_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
