<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251117033951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE social_network (id VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(100) NOT NULL, url VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_EFFF522177153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_social_network (id VARCHAR(255) NOT NULL, user_id VARCHAR(36) NOT NULL, social_network_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', username VARCHAR(255) NOT NULL, INDEX IDX_847A8D78A76ED395 (user_id), INDEX IDX_847A8D78FA413953 (social_network_id), UNIQUE INDEX unique_user_social_network (user_id, social_network_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_social_network ADD CONSTRAINT FK_847A8D78A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_social_network ADD CONSTRAINT FK_847A8D78FA413953 FOREIGN KEY (social_network_id) REFERENCES social_network (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_social_network DROP FOREIGN KEY FK_847A8D78A76ED395');
        $this->addSql('ALTER TABLE user_social_network DROP FOREIGN KEY FK_847A8D78FA413953');
        $this->addSql('DROP TABLE social_network');
        $this->addSql('DROP TABLE user_social_network');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
