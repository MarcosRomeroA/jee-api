<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration: Move creator/leader from Team to TeamUser
 *
 * This migration:
 * 1. Adds is_creator and is_leader columns to team_user
 * 2. Migrates existing creator/leader data from team to team_user
 * 3. Removes creator_id and leader_id from team table
 */
final class Version20251128230156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Move creator and leader relationships from Team to TeamUser with boolean flags';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Add new columns to team_user (with IF NOT EXISTS logic via checking)
        $this->addSql('ALTER TABLE team_user ADD is_creator TINYINT(1) DEFAULT 0 NOT NULL, ADD is_leader TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('CREATE INDEX IDX_TEAM_USER_CREATOR ON team_user (is_creator)');
        $this->addSql('CREATE INDEX IDX_TEAM_USER_LEADER ON team_user (is_leader)');

        // Step 2: Migrate existing creator data - set is_creator=true for existing team_user entries
        $this->addSql('
            UPDATE team_user tu
            INNER JOIN team t ON tu.team_id = t.id
            SET tu.is_creator = 1
            WHERE tu.user_id = t.creator_id
        ');

        // Step 3: Migrate existing leader data - set is_leader=true for existing team_user entries
        $this->addSql('
            UPDATE team_user tu
            INNER JOIN team t ON tu.team_id = t.id
            SET tu.is_leader = 1
            WHERE tu.user_id = t.leader_id
        ');

        // Step 4: Insert creators who are NOT already in team_user (they should be members too)
        $this->addSql('
            INSERT INTO team_user (id, team_id, user_id, is_creator, is_leader, joined_at)
            SELECT UUID(), t.id, t.creator_id, 1, 0, NOW()
            FROM team t
            WHERE NOT EXISTS (
                SELECT 1 FROM team_user tu
                WHERE tu.team_id = t.id AND tu.user_id = t.creator_id
            )
        ');

        // Step 5: Insert leaders who are NOT already in team_user (they should be members too)
        $this->addSql('
            INSERT INTO team_user (id, team_id, user_id, is_creator, is_leader, joined_at)
            SELECT UUID(), t.id, t.leader_id, 0, 1, NOW()
            FROM team t
            WHERE t.leader_id != t.creator_id
            AND NOT EXISTS (
                SELECT 1 FROM team_user tu
                WHERE tu.team_id = t.id AND tu.user_id = t.leader_id
            )
        ');

        // Step 6: If creator is also leader, ensure is_leader is set
        $this->addSql('
            UPDATE team_user tu
            INNER JOIN team t ON tu.team_id = t.id
            SET tu.is_leader = 1
            WHERE tu.user_id = t.leader_id AND tu.is_creator = 1
        ');

        // Step 7: Drop foreign keys and columns from team table
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F61220EA6');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F73154ED4');
        $this->addSql('DROP INDEX IDX_C4E0A61F61220EA6 ON team');
        $this->addSql('DROP INDEX IDX_C4E0A61F73154ED4 ON team');
        $this->addSql('ALTER TABLE team DROP creator_id, DROP leader_id');
    }

    public function down(Schema $schema): void
    {
        // Step 1: Re-add columns to team
        $this->addSql('ALTER TABLE team ADD creator_id VARCHAR(36) NOT NULL, ADD leader_id VARCHAR(36) NOT NULL');

        // Step 2: Restore creator_id from team_user
        $this->addSql('
            UPDATE team t
            INNER JOIN team_user tu ON tu.team_id = t.id AND tu.is_creator = 1
            SET t.creator_id = tu.user_id
        ');

        // Step 3: Restore leader_id from team_user
        $this->addSql('
            UPDATE team t
            INNER JOIN team_user tu ON tu.team_id = t.id AND tu.is_leader = 1
            SET t.leader_id = tu.user_id
        ');

        // Step 4: Add back foreign keys and indexes
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F73154ED4 FOREIGN KEY (leader_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F61220EA6 ON team (creator_id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F73154ED4 ON team (leader_id)');

        // Step 5: Remove columns and indexes from team_user
        $this->addSql('DROP INDEX IDX_TEAM_USER_CREATOR ON team_user');
        $this->addSql('DROP INDEX IDX_TEAM_USER_LEADER ON team_user');
        $this->addSql('ALTER TABLE team_user DROP is_creator, DROP is_leader');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
