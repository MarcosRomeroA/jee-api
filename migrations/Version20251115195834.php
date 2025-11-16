<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to remove game_id column from team table
 * Teams now use team_game table to manage multiple games
 */
final class Version20251115195834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove game_id column from team table as teams can now have multiple games via team_game table';
    }

    public function up(Schema $schema): void
    {
        // Remove the foreign key constraint first
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FE48FD905');
        // Remove the index
        $this->addSql('DROP INDEX IDX_C4E0A61FE48FD905 ON team');
        // Remove the game_id column
        $this->addSql('ALTER TABLE team DROP game_id');
    }

    public function down(Schema $schema): void
    {
        // Add back the game_id column
        $this->addSql('ALTER TABLE team ADD game_id VARCHAR(36) NOT NULL');
        // Add back the index
        $this->addSql('CREATE INDEX IDX_C4E0A61FE48FD905 ON team (game_id)');
        // Add back the foreign key constraint
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
