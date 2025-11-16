<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to:
 * 1. Add lastMessage to Conversation
 * 2. Change Player-GameRole relationship from ManyToOne to ManyToMany
 * 3. Make GameRank optional in Player
 * 4. Create Unranked GameRank for all games
 */
final class Version20251116053330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add lastMessage to Conversation, change Player to ManyToMany GameRole, make GameRank optional, add Unranked rank';
    }

    public function up(Schema $schema): void
    {
        // 1. Add last_message_id to conversation table (skip if already exists from old migrations)
        $this->addSql('SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "conversation" AND COLUMN_NAME = "last_message_id")');
        $this->addSql('SET @sqlstmt := IF(@exist = 0, "ALTER TABLE conversation ADD COLUMN last_message_id VARCHAR(36) DEFAULT NULL", "SELECT \'Column already exists\' AS note")');
        $this->addSql('PREPARE stmt FROM @sqlstmt');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        $this->addSql('SET @fk_exist := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "conversation" AND CONSTRAINT_NAME = "FK_8A8E26E9BA0E79C3")');
        $this->addSql('SET @sqlstmt := IF(@fk_exist = 0, "ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9BA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id)", "SELECT \'FK already exists\' AS note")');
        $this->addSql('PREPARE stmt FROM @sqlstmt');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        $this->addSql('SET @idx_exist := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "conversation" AND INDEX_NAME = "IDX_8A8E26E9BA0E79C3")');
        $this->addSql('SET @sqlstmt := IF(@idx_exist = 0, "CREATE INDEX IDX_8A8E26E9BA0E79C3 ON conversation (last_message_id)", "SELECT \'Index already exists\' AS note")');
        $this->addSql('PREPARE stmt FROM @sqlstmt');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        // 2. Create player_game_role junction table (skip if already exists)
        $this->addSql('SET @tbl_exist := (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "player_game_role")');
        $this->addSql('SET @sqlstmt := IF(@tbl_exist = 0, "CREATE TABLE player_game_role (player_id VARCHAR(36) NOT NULL, game_role_id VARCHAR(36) NOT NULL, INDEX IDX_1C70D37F99E6F5DF (player_id), INDEX IDX_1C70D37F43C15D83 (game_role_id), PRIMARY KEY(player_id, game_role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB", "SELECT \'Table already exists\' AS note")');
        $this->addSql('PREPARE stmt FROM @sqlstmt');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        // 3. Migrate existing player game_role_id to junction table (only if game_role_id exists)
        $this->addSql('SET @col_exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "player" AND COLUMN_NAME = "game_role_id")');
        $this->addSql('SET @sqlstmt := IF(@col_exist > 0, "INSERT IGNORE INTO player_game_role (player_id, game_role_id) SELECT id, game_role_id FROM player WHERE game_role_id IS NOT NULL", "SELECT \'No data to migrate\' AS note")');
        $this->addSql('PREPARE stmt FROM @sqlstmt');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        // 4. Add foreign keys to junction table (skip if already exists)
        $this->addSql('SET @fk1_exist := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "player_game_role" AND CONSTRAINT_NAME = "FK_1C70D37F99E6F5DF")');
        $this->addSql('SET @sqlstmt := IF(@fk1_exist = 0, "ALTER TABLE player_game_role ADD CONSTRAINT FK_1C70D37F99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE", "SELECT \'FK1 already exists\' AS note")');
        $this->addSql('PREPARE stmt FROM @sqlstmt');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        $this->addSql('SET @fk2_exist := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "player_game_role" AND CONSTRAINT_NAME = "FK_1C70D37F43C15D83")');
        $this->addSql('SET @sqlstmt := IF(@fk2_exist = 0, "ALTER TABLE player_game_role ADD CONSTRAINT FK_1C70D37F43C15D83 FOREIGN KEY (game_role_id) REFERENCES game_role (id) ON DELETE CASCADE", "SELECT \'FK2 already exists\' AS note")');
        $this->addSql('PREPARE stmt FROM @sqlstmt');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        // 5. Drop old game_role_id from player table (only if it exists)
        $this->addSql('SET @col_exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "player" AND COLUMN_NAME = "game_role_id")');
        $this->addSql('SET @sqlstmt := IF(@col_exist > 0, "ALTER TABLE player DROP FOREIGN KEY FK_98197A6543C15D83", "SELECT \'FK does not exist\' AS note")');
        $this->addSql('PREPARE stmt FROM @sqlstmt');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        $this->addSql('SET @sqlstmt := IF(@col_exist > 0, "DROP INDEX IDX_98197A6543C15D83 ON player", "SELECT \'Index does not exist\' AS note")');
        $this->addSql('PREPARE stmt FROM @sqlstmt');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        $this->addSql('SET @sqlstmt := IF(@col_exist > 0, "ALTER TABLE player DROP COLUMN game_role_id", "SELECT \'Column does not exist\' AS note")');
        $this->addSql('PREPARE stmt FROM @sqlstmt');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        // 6. Make game_rank_id nullable
        $this->addSql('ALTER TABLE player MODIFY game_rank_id VARCHAR(36) DEFAULT NULL');

        // 7. Create Unranked GameRank for all existing games (if they don't already exist)
        $this->addSql("
            INSERT IGNORE INTO game_rank (id, game_id, rank_id, level)
            SELECT
                CONCAT('00000000-0000-0000-0000-', LPAD(CONV(SUBSTRING(MD5(game.id), 1, 12), 16, 10), 12, '0')),
                game.id,
                '00000000-0000-0000-0000-000000000001',
                0
            FROM game
            LEFT JOIN game_rank ON game_rank.game_id = game.id AND game_rank.rank_id = '00000000-0000-0000-0000-000000000001'
            WHERE game_rank.id IS NULL
        ");
    }

    public function down(Schema $schema): void
    {
        // Reverse: Remove Unranked ranks
        $this->addSql("DELETE FROM game_rank WHERE rank_id = '00000000-0000-0000-0000-000000000001' AND level = 0");

        // Reverse: Add back game_role_id to player (take first role from junction table)
        $this->addSql('ALTER TABLE player ADD game_role_id VARCHAR(36) NOT NULL');
        $this->addSql('UPDATE player p
                       SET game_role_id = (
                           SELECT game_role_id FROM player_game_role pgr
                           WHERE pgr.player_id = p.id LIMIT 1
                       )');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A6543C15D83 FOREIGN KEY (game_role_id) REFERENCES game_role (id)');
        $this->addSql('CREATE INDEX IDX_98197A6543C15D83 ON player (game_role_id)');

        // Reverse: Make game_rank_id NOT NULL again
        $this->addSql('ALTER TABLE player MODIFY game_rank_id VARCHAR(36) NOT NULL');

        // Reverse: Drop junction table
        $this->addSql('DROP TABLE player_game_role');

        // Reverse: Remove last_message_id from conversation
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9BA0E79C3');
        $this->addSql('DROP INDEX IDX_8A8E26E9BA0E79C3 ON conversation');
        $this->addSql('ALTER TABLE conversation DROP COLUMN last_message_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
