<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251117053932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hashtag (id VARCHAR(255) NOT NULL, tag VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5AB52A61389B783 (tag), INDEX idx_hashtag_tag (tag), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_hashtag (post_id VARCHAR(36) NOT NULL, hashtag_id VARCHAR(255) NOT NULL, INDEX IDX_675D9D524B89032C (post_id), INDEX IDX_675D9D52FB34EF56 (hashtag_id), PRIMARY KEY(post_id, hashtag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post_hashtag ADD CONSTRAINT FK_675D9D524B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_hashtag ADD CONSTRAINT FK_675D9D52FB34EF56 FOREIGN KEY (hashtag_id) REFERENCES hashtag (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_EFFF522177153098 ON social_network');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_hashtag DROP FOREIGN KEY FK_675D9D524B89032C');
        $this->addSql('ALTER TABLE post_hashtag DROP FOREIGN KEY FK_675D9D52FB34EF56');
        $this->addSql('DROP TABLE hashtag');
        $this->addSql('DROP TABLE post_hashtag');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EFFF522177153098 ON social_network (code)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
