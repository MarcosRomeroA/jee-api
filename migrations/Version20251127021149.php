<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251127021149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin CHANGE role role VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE post ADD disabled TINYINT(1) DEFAULT 0 NOT NULL, ADD moderation_reason VARCHAR(255) DEFAULT NULL, ADD disabled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` CHANGE role role VARCHAR(20) DEFAULT \'admin\' NOT NULL');
        $this->addSql('ALTER TABLE post DROP disabled, DROP moderation_reason, DROP disabled_at');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
