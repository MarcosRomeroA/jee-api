<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251207005817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post_resource ADD image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE roster ADD logo_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE team ADD image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD background_image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE tournament ADD image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD background_image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user ADD background_image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP image_updated_at');
        $this->addSql('ALTER TABLE post_resource DROP image_updated_at');
        $this->addSql('ALTER TABLE roster DROP logo_updated_at');
        $this->addSql('ALTER TABLE team DROP image_updated_at, DROP background_image_updated_at');
        $this->addSql('ALTER TABLE tournament DROP image_updated_at, DROP background_image_updated_at');
        $this->addSql('ALTER TABLE `user` DROP background_image_updated_at');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
