<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251113070001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change tournament_status.id column type from VARCHAR(50) to VARCHAR(36) for UUID support';
    }

    public function up(Schema $schema): void
    {
        // Primero eliminar la foreign key constraint
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9BB77A8AC');

        // Cambiar el tipo de columna en tournament_status
        $this->addSql('ALTER TABLE tournament_status MODIFY id VARCHAR(36) NOT NULL');

        // Cambiar el tipo de columna en tournament
        $this->addSql('ALTER TABLE tournament MODIFY tournament_status_id VARCHAR(36) NOT NULL');

        // Recrear la foreign key constraint
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9BB77A8AC FOREIGN KEY (tournament_status_id) REFERENCES tournament_status (id)');
    }

    public function down(Schema $schema): void
    {
        // Primero eliminar la foreign key constraint
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9BB77A8AC');

        // Revertir el tipo de columna en tournament_status
        $this->addSql('ALTER TABLE tournament_status MODIFY id VARCHAR(50) NOT NULL');

        // Revertir el tipo de columna en tournament
        $this->addSql('ALTER TABLE tournament MODIFY tournament_status_id VARCHAR(50) NOT NULL');

        // Recrear la foreign key constraint
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9BB77A8AC FOREIGN KEY (tournament_status_id) REFERENCES tournament_status (id)');
    }
}

