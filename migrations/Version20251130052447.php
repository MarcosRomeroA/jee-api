<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251130052447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create game_account_requirement table and seed data for existing games';
    }

    public function up(Schema $schema): void
    {
        // Create table
        $this->addSql('CREATE TABLE game_account_requirement (id VARCHAR(36) NOT NULL, game_id VARCHAR(36) NOT NULL, requirements JSON NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_F14F9202E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_account_requirement ADD CONSTRAINT FK_F14F9202E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');

        // Seed data for existing games
        // Valorant: region, username, tag
        $this->addSql("INSERT INTO game_account_requirement (id, game_id, requirements) VALUES (UUID(), '550e8400-e29b-41d4-a716-446655440080', '{\"region\": true, \"username\": true, \"tag\": true}')");

        // League of Legends: region, username, tag
        $this->addSql("INSERT INTO game_account_requirement (id, game_id, requirements) VALUES (UUID(), '550e8400-e29b-41d4-a716-446655440081', '{\"region\": true, \"username\": true, \"tag\": true}')");

        // Counter-Strike 2: steam_profile
        $this->addSql("INSERT INTO game_account_requirement (id, game_id, requirements) VALUES (UUID(), '550e8400-e29b-41d4-a716-446655440082', '{\"steam_profile\": true}')");

        // Dota 2: steam_profile
        $this->addSql("INSERT INTO game_account_requirement (id, game_id, requirements) VALUES (UUID(), '550e8400-e29b-41d4-a716-446655440083', '{\"steam_profile\": true}')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_account_requirement DROP FOREIGN KEY FK_F14F9202E48FD905');
        $this->addSql('DROP TABLE game_account_requirement');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
