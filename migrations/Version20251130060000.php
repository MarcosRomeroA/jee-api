<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251130060000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add moderation fields to Team and Tournament tables';
    }

    public function up(Schema $schema): void
    {
        // Team moderation fields
        $this->addSql('ALTER TABLE team ADD is_disabled TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE team ADD disabled_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE team ADD moderation_reason VARCHAR(50) DEFAULT NULL');

        // Tournament moderation fields
        $this->addSql('ALTER TABLE tournament ADD is_disabled TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE tournament ADD disabled_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament ADD moderation_reason VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Team
        $this->addSql('ALTER TABLE team DROP is_disabled');
        $this->addSql('ALTER TABLE team DROP disabled_at');
        $this->addSql('ALTER TABLE team DROP moderation_reason');

        // Tournament
        $this->addSql('ALTER TABLE tournament DROP is_disabled');
        $this->addSql('ALTER TABLE tournament DROP disabled_at');
        $this->addSql('ALTER TABLE tournament DROP moderation_reason');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
