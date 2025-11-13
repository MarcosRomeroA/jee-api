<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to support multiple roles per player and optional rank
 */
final class Version20251113043200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create player_role table for many-to-many relationship and make game_rank_id nullable';
    }

    public function up(Schema $schema): void
    {
        // Create player_role table
        $this->addSql('CREATE TABLE player_role (id VARCHAR(36) NOT NULL, player_id VARCHAR(36) NOT NULL, game_role_id VARCHAR(36) NOT NULL, INDEX IDX_E43EE9E899E6F5DF (player_id), INDEX IDX_E43EE9E843C15D83 (game_role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_role ADD CONSTRAINT FK_E43EE9E899E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE player_role ADD CONSTRAINT FK_E43EE9E843C15D83 FOREIGN KEY (game_role_id) REFERENCES game_role (id)');
        
        // Migrate existing data from player.game_role_id to player_role table
        $this->addSql('INSERT INTO player_role (id, player_id, game_role_id) SELECT UUID(), id, game_role_id FROM player WHERE game_role_id IS NOT NULL');
        
        // Drop the old foreign key and column
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A6543C15D83');
        $this->addSql('DROP INDEX IDX_98197A6543C15D83 ON player');
        $this->addSql('ALTER TABLE player DROP game_role_id');
        
        // Make game_rank_id nullable
        $this->addSql('ALTER TABLE player CHANGE game_rank_id game_rank_id VARCHAR(36) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Add back game_role_id column to player table
        $this->addSql('ALTER TABLE player ADD game_role_id VARCHAR(36) DEFAULT NULL');
        
        // Migrate data back - take the first role from player_role
        $this->addSql('UPDATE player p SET game_role_id = (SELECT game_role_id FROM player_role WHERE player_id = p.id LIMIT 1)');
        
        // Make game_role_id NOT NULL
        $this->addSql('ALTER TABLE player CHANGE game_role_id game_role_id VARCHAR(36) NOT NULL');
        $this->addSql('CREATE INDEX IDX_98197A6543C15D83 ON player (game_role_id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A6543C15D83 FOREIGN KEY (game_role_id) REFERENCES game_role (id)');
        
        // Make game_rank_id NOT NULL again
        $this->addSql('ALTER TABLE player CHANGE game_rank_id game_rank_id VARCHAR(36) NOT NULL');
        
        // Drop player_role table
        $this->addSql('ALTER TABLE player_role DROP FOREIGN KEY FK_E43EE9E899E6F5DF');
        $this->addSql('ALTER TABLE player_role DROP FOREIGN KEY FK_E43EE9E843C15D83');
        $this->addSql('DROP TABLE player_role');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
