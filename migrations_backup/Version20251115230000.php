<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251115230000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make team creator_id and leader_id NOT NULL';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team MODIFY creator_id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE team MODIFY leader_id VARCHAR(36) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team MODIFY creator_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE team MODIFY leader_id VARCHAR(36) DEFAULT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
