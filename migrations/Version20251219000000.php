<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251219000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add comment_id column to notification table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification ADD comment_id VARCHAR(36) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification DROP comment_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}

