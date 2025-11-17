<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Seed social networks
 */
final class Version20251117034500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed initial social networks (Twitch, YouTube, Twitter, Instagram, Facebook, TikTok)';
    }

    public function up(Schema $schema): void
    {
        // Insert social networks
        $socialNetworks = [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440090',
                'name' => 'Twitch',
                'code' => 'twitch',
                'url' => 'https://www.twitch.tv/'
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440091',
                'name' => 'YouTube',
                'code' => 'youtube',
                'url' => 'https://www.youtube.com/@'
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440092',
                'name' => 'Twitter',
                'code' => 'twitter',
                'url' => 'https://www.twitter.com/'
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440093',
                'name' => 'Instagram',
                'code' => 'instagram',
                'url' => 'https://www.instagram.com/'
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440094',
                'name' => 'Facebook',
                'code' => 'facebook',
                'url' => 'https://www.facebook.com/'
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440095',
                'name' => 'TikTok',
                'code' => 'tiktok',
                'url' => 'https://www.tiktok.com/@'
            ],
        ];

        foreach ($socialNetworks as $network) {
            $this->addSql(
                'INSERT INTO social_network (id, name, code, url) VALUES (:id, :name, :code, :url)',
                $network
            );
        }
    }

    public function down(Schema $schema): void
    {
        // Delete all social networks
        $this->addSql('DELETE FROM social_network WHERE code IN (\'twitch\', \'youtube\', \'twitter\', \'instagram\', \'facebook\', \'tiktok\')');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
