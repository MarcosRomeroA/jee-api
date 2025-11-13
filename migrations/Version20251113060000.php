<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251113060000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add description and created_at columns to user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` DROP description');
        $this->addSql('ALTER TABLE `user` DROP created_at');
    }
}

