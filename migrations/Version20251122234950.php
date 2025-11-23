<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251122234950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE team_user (id VARCHAR(36) NOT NULL, team_id VARCHAR(36) NOT NULL, user_id VARCHAR(36) NOT NULL, joined_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5C722232296CD8AE (team_id), INDEX IDX_5C722232A76ED395 (user_id), UNIQUE INDEX UNIQ_TEAM_USER (team_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE team_request ADD user_id VARCHAR(36) NOT NULL, CHANGE player_id player_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE team_request ADD CONSTRAINT FK_C4EEFEA8A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_C4EEFEA8A76ED395 ON team_request (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232296CD8AE');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232A76ED395');
        $this->addSql('DROP TABLE team_user');
        $this->addSql('ALTER TABLE team_request DROP FOREIGN KEY FK_C4EEFEA8A76ED395');
        $this->addSql('DROP INDEX IDX_C4EEFEA8A76ED395 ON team_request');
        $this->addSql('ALTER TABLE team_request DROP user_id, CHANGE player_id player_id VARCHAR(36) NOT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
