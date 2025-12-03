<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251203024834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE roster (id VARCHAR(36) NOT NULL, team_id VARCHAR(36) NOT NULL, game_id VARCHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, description TEXT DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_ROSTER_TEAM (team_id), INDEX IDX_ROSTER_GAME (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roster_player (id VARCHAR(36) NOT NULL, roster_id VARCHAR(36) NOT NULL, player_id VARCHAR(36) NOT NULL, game_role_id VARCHAR(36) DEFAULT NULL, is_starter TINYINT(1) DEFAULT 0 NOT NULL, is_leader TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_13BF7DBA75404483 (roster_id), INDEX IDX_13BF7DBA99E6F5DF (player_id), INDEX IDX_13BF7DBA43C15D83 (game_role_id), INDEX IDX_ROSTER_PLAYER_STARTER (is_starter), INDEX IDX_ROSTER_PLAYER_LEADER (is_leader), UNIQUE INDEX UNIQ_ROSTER_PLAYER (roster_id, player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE roster ADD CONSTRAINT FK_60B9ADF9296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE roster ADD CONSTRAINT FK_60B9ADF9E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE roster_player ADD CONSTRAINT FK_13BF7DBA75404483 FOREIGN KEY (roster_id) REFERENCES roster (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE roster_player ADD CONSTRAINT FK_13BF7DBA99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE roster_player ADD CONSTRAINT FK_13BF7DBA43C15D83 FOREIGN KEY (game_role_id) REFERENCES game_role (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE roster DROP FOREIGN KEY FK_60B9ADF9296CD8AE');
        $this->addSql('ALTER TABLE roster DROP FOREIGN KEY FK_60B9ADF9E48FD905');
        $this->addSql('ALTER TABLE roster_player DROP FOREIGN KEY FK_13BF7DBA75404483');
        $this->addSql('ALTER TABLE roster_player DROP FOREIGN KEY FK_13BF7DBA99E6F5DF');
        $this->addSql('ALTER TABLE roster_player DROP FOREIGN KEY FK_13BF7DBA43C15D83');
        $this->addSql('DROP TABLE roster');
        $this->addSql('DROP TABLE roster_player');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
