<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113154633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_follow ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_follow MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON user_follow');
        $this->addSql('ALTER TABLE user_follow DROP id');
        $this->addSql('ALTER TABLE user_follow ADD PRIMARY KEY (follower_id, followed_id)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
