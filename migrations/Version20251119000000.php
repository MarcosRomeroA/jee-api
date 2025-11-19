<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Reset tournament image field to null
 */
final class Version20251119000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Reset tournament image field to null for base64 upload migration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE tournament SET image = NULL WHERE image IS NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // No rollback needed - images will be re-uploaded
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
