<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251111024501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id VARCHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, min_players_quantity INT NOT NULL, max_players_quantity INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_rank (id VARCHAR(36) NOT NULL, game_id VARCHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, level INT NOT NULL, INDEX IDX_636C84C9E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_role (id VARCHAR(36) NOT NULL, role_id VARCHAR(36) NOT NULL, game_id VARCHAR(36) NOT NULL, INDEX IDX_BC7CE646D60322AC (role_id), INDEX IDX_BC7CE646E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id VARCHAR(36) NOT NULL, user_id VARCHAR(36) NOT NULL, game_role_id VARCHAR(36) NOT NULL, game_rank_id VARCHAR(36) NOT NULL, verified TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, username VARCHAR(100) NOT NULL, INDEX IDX_98197A65A76ED395 (user_id), INDEX IDX_98197A6543C15D83 (game_role_id), INDEX IDX_98197A65E3D418A0 (game_rank_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id VARCHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id VARCHAR(36) NOT NULL, game_id VARCHAR(36) NOT NULL, owner_id VARCHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_C4E0A61FE48FD905 (game_id), INDEX IDX_C4E0A61F7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_player (id VARCHAR(36) NOT NULL, team_id VARCHAR(36) NOT NULL, player_id VARCHAR(36) NOT NULL, joined_at DATETIME NOT NULL, INDEX IDX_EE023DBC296CD8AE (team_id), INDEX IDX_EE023DBC99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_request (id VARCHAR(36) NOT NULL, team_id VARCHAR(36) NOT NULL, player_id VARCHAR(36) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C4EEFEA8296CD8AE (team_id), INDEX IDX_C4EEFEA899E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament (id VARCHAR(36) NOT NULL, game_id VARCHAR(36) NOT NULL, tournament_status_id VARCHAR(50) NOT NULL, min_game_rank_id VARCHAR(36) DEFAULT NULL, max_game_rank_id VARCHAR(36) DEFAULT NULL, responsible_id VARCHAR(36) NOT NULL, name VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, registered_teams INT DEFAULT 0 NOT NULL, max_teams INT NOT NULL, is_official TINYINT(1) DEFAULT 0 NOT NULL, image VARCHAR(255) DEFAULT NULL, prize VARCHAR(255) DEFAULT NULL, region VARCHAR(100) DEFAULT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_BD5FB8D9E48FD905 (game_id), INDEX IDX_BD5FB8D9BB77A8AC (tournament_status_id), INDEX IDX_BD5FB8D919413025 (min_game_rank_id), INDEX IDX_BD5FB8D948B88B78 (max_game_rank_id), INDEX IDX_BD5FB8D9602AD315 (responsible_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament_request (id VARCHAR(36) NOT NULL, tournament_id VARCHAR(36) NOT NULL, team_id VARCHAR(36) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_5098749533D1A3E7 (tournament_id), INDEX IDX_50987495296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament_status (id VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament_team (id VARCHAR(36) NOT NULL, tournament_id VARCHAR(36) NOT NULL, team_id VARCHAR(36) NOT NULL, registered_at DATETIME NOT NULL, INDEX IDX_F36D142133D1A3E7 (tournament_id), INDEX IDX_F36D1421296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_rank ADD CONSTRAINT FK_636C84C9E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_role ADD CONSTRAINT FK_BC7CE646D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE game_role ADD CONSTRAINT FK_BC7CE646E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A6543C15D83 FOREIGN KEY (game_role_id) REFERENCES game_role (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65E3D418A0 FOREIGN KEY (game_rank_id) REFERENCES game_rank (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE team_player ADD CONSTRAINT FK_EE023DBC296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE team_player ADD CONSTRAINT FK_EE023DBC99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE team_request ADD CONSTRAINT FK_C4EEFEA8296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE team_request ADD CONSTRAINT FK_C4EEFEA899E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9BB77A8AC FOREIGN KEY (tournament_status_id) REFERENCES tournament_status (id)');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D919413025 FOREIGN KEY (min_game_rank_id) REFERENCES game_rank (id)');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D948B88B78 FOREIGN KEY (max_game_rank_id) REFERENCES game_rank (id)');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9602AD315 FOREIGN KEY (responsible_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE tournament_request ADD CONSTRAINT FK_5098749533D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE tournament_request ADD CONSTRAINT FK_50987495296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE tournament_team ADD CONSTRAINT FK_F36D142133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE tournament_team ADD CONSTRAINT FK_F36D1421296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_rank DROP FOREIGN KEY FK_636C84C9E48FD905');
        $this->addSql('ALTER TABLE game_role DROP FOREIGN KEY FK_BC7CE646D60322AC');
        $this->addSql('ALTER TABLE game_role DROP FOREIGN KEY FK_BC7CE646E48FD905');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65A76ED395');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A6543C15D83');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65E3D418A0');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FE48FD905');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F7E3C61F9');
        $this->addSql('ALTER TABLE team_player DROP FOREIGN KEY FK_EE023DBC296CD8AE');
        $this->addSql('ALTER TABLE team_player DROP FOREIGN KEY FK_EE023DBC99E6F5DF');
        $this->addSql('ALTER TABLE team_request DROP FOREIGN KEY FK_C4EEFEA8296CD8AE');
        $this->addSql('ALTER TABLE team_request DROP FOREIGN KEY FK_C4EEFEA899E6F5DF');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9E48FD905');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9BB77A8AC');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D919413025');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D948B88B78');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9602AD315');
        $this->addSql('ALTER TABLE tournament_request DROP FOREIGN KEY FK_5098749533D1A3E7');
        $this->addSql('ALTER TABLE tournament_request DROP FOREIGN KEY FK_50987495296CD8AE');
        $this->addSql('ALTER TABLE tournament_team DROP FOREIGN KEY FK_F36D142133D1A3E7');
        $this->addSql('ALTER TABLE tournament_team DROP FOREIGN KEY FK_F36D1421296CD8AE');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_rank');
        $this->addSql('DROP TABLE game_role');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_player');
        $this->addSql('DROP TABLE team_request');
        $this->addSql('DROP TABLE tournament');
        $this->addSql('DROP TABLE tournament_request');
        $this->addSql('DROP TABLE tournament_status');
        $this->addSql('DROP TABLE tournament_team');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
