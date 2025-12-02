<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add game_id column to player table.
 * This allows players to have optional game roles while still being associated with a game.
 */
final class Version20251202180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add game_id column to player table';
    }

    public function up(Schema $schema): void
    {
        // First add the column as nullable
        $this->addSql('ALTER TABLE player ADD game_id VARCHAR(36) DEFAULT NULL');

        // Populate game_id from existing game roles (get game_id from the first game_role)
        $this->addSql('
            UPDATE player p
            SET p.game_id = (
                SELECT gr.game_id
                FROM player_game_role pgr
                JOIN game_role gr ON pgr.game_role_id = gr.id
                WHERE pgr.player_id = p.id
                LIMIT 1
            )
            WHERE p.game_id IS NULL
        ');

        // Make the column NOT NULL after populating data
        $this->addSql('ALTER TABLE player MODIFY game_id VARCHAR(36) NOT NULL');

        // Add foreign key constraint
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');

        // Add index for better performance
        $this->addSql('CREATE INDEX IDX_98197A65E48FD905 ON player (game_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65E48FD905');
        $this->addSql('DROP INDEX IDX_98197A65E48FD905 ON player');
        $this->addSql('ALTER TABLE player DROP game_id');
    }
}
