<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204192758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add moderation fields to post_comment and comment_moderated notification type';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_comment ADD disabled TINYINT(1) DEFAULT 0 NOT NULL, ADD moderation_reason VARCHAR(255) DEFAULT NULL, ADD disabled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX IDX_COMMENT_DISABLED ON post_comment (disabled)');

        // Add comment_moderated notification type
        $this->addSql("INSERT INTO notification_type (id, name) VALUES (UUID(), 'comment_moderated')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_COMMENT_DISABLED ON post_comment');
        $this->addSql('ALTER TABLE post_comment DROP disabled, DROP moderation_reason, DROP disabled_at');

        // Remove comment_moderated notification type
        $this->addSql("DELETE FROM notification_type WHERE name = 'comment_moderated'");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
