<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112145225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `match` (id VARCHAR(36) NOT NULL, tournament_id VARCHAR(36) NOT NULL, name VARCHAR(100) DEFAULT NULL, round INT NOT NULL, status VARCHAR(50) NOT NULL, scheduled_at DATETIME DEFAULT NULL, started_at DATETIME DEFAULT NULL, completed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_7A5BC50533D1A3E7 (tournament_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE match_participant (id VARCHAR(36) NOT NULL, match_id VARCHAR(36) NOT NULL, team_id VARCHAR(36) NOT NULL, position INT NOT NULL, is_winner TINYINT(1) DEFAULT 0 NOT NULL, score INT NOT NULL, INDEX IDX_E5061A392ABEACD6 (match_id), INDEX IDX_E5061A39296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `match` ADD CONSTRAINT FK_7A5BC50533D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE match_participant ADD CONSTRAINT FK_E5061A392ABEACD6 FOREIGN KEY (match_id) REFERENCES `match` (id)');
        $this->addSql('ALTER TABLE match_participant ADD CONSTRAINT FK_E5061A39296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `match` DROP FOREIGN KEY FK_7A5BC50533D1A3E7');
        $this->addSql('ALTER TABLE match_participant DROP FOREIGN KEY FK_E5061A392ABEACD6');
        $this->addSql('ALTER TABLE match_participant DROP FOREIGN KEY FK_E5061A39296CD8AE');
        $this->addSql('DROP TABLE `match`');
        $this->addSql('DROP TABLE match_participant');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
