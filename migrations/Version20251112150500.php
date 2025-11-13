<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112150500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rank (id VARCHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE game_rank ADD rank_id VARCHAR(36) NOT NULL, DROP name');
        $this->addSql('ALTER TABLE game_rank ADD CONSTRAINT FK_636C84C97616678F FOREIGN KEY (rank_id) REFERENCES rank (id)');
        $this->addSql('CREATE INDEX IDX_636C84C97616678F ON game_rank (rank_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_rank DROP FOREIGN KEY FK_636C84C97616678F');
        $this->addSql('DROP TABLE rank');
        $this->addSql('ALTER TABLE game CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('DROP INDEX IDX_636C84C97616678F ON game_rank');
        $this->addSql('ALTER TABLE game_rank ADD name VARCHAR(100) NOT NULL, DROP rank_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
