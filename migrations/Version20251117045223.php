<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Update social network URLs to include proper separators for username concatenation
 */
final class Version20251117045223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update social network URLs to include proper separators (/ or /@)';
    }

    public function up(Schema $schema): void
    {
        // Update URLs to include proper separators
        $this->addSql("UPDATE social_network SET url = 'https://www.twitch.tv/' WHERE code = 'twitch'");
        $this->addSql("UPDATE social_network SET url = 'https://www.youtube.com/@' WHERE code = 'youtube'");
        $this->addSql("UPDATE social_network SET url = 'https://www.twitter.com/' WHERE code = 'twitter'");
        $this->addSql("UPDATE social_network SET url = 'https://www.instagram.com/' WHERE code = 'instagram'");
        $this->addSql("UPDATE social_network SET url = 'https://www.facebook.com/' WHERE code = 'facebook'");
        $this->addSql("UPDATE social_network SET url = 'https://www.tiktok.com/@' WHERE code = 'tiktok'");
    }

    public function down(Schema $schema): void
    {
        // Revert URLs to original format (without separators)
        $this->addSql("UPDATE social_network SET url = 'https://www.twitch.tv' WHERE code = 'twitch'");
        $this->addSql("UPDATE social_network SET url = 'https://www.youtube.com' WHERE code = 'youtube'");
        $this->addSql("UPDATE social_network SET url = 'https://www.twitter.com' WHERE code = 'twitter'");
        $this->addSql("UPDATE social_network SET url = 'https://www.instagram.com' WHERE code = 'instagram'");
        $this->addSql("UPDATE social_network SET url = 'https://www.facebook.com' WHERE code = 'facebook'");
        $this->addSql("UPDATE social_network SET url = 'https://www.tiktok.com' WHERE code = 'tiktok'");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
