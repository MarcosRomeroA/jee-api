<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250911230915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO notification_type (id, name) VALUES (:id, :name)', [
            'id' => Uuid::random()->value(),
            'name' => 'new_message',
        ]);

        $this->addSql('INSERT INTO notification_type (id, name) VALUES (:id, :name)', [
            'id' => Uuid::random()->value(),
            'name' => 'post_liked',
        ]);

        $this->addSql('INSERT INTO notification_type (id, name) VALUES (:id, :name)', [
            'id' => Uuid::random()->value(),
            'name' => 'post_commented',
        ]);

        $this->addSql('INSERT INTO notification_type (id, name) VALUES (:id, :name)', [
            'id' => Uuid::random()->value(),
            'name' => 'post_shared',
        ]);

        $this->addSql('INSERT INTO notification_type (id, name) VALUES (:id, :name)', [
            'id' => Uuid::random()->value(),
            'name' => 'new_follower',
        ]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM notification_type WHERE name IS NOT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
