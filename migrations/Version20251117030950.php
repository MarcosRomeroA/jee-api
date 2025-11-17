<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251117030950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        // Limpiar datos existentes ya que el formato del token cambia de 64 a 36 caracteres
        $this->addSql('TRUNCATE TABLE email_confirmation');

        // Cambiar el tamaño de la columna token de 64 a 36 caracteres (UUID format)
        $this->addSql('ALTER TABLE email_confirmation CHANGE token token VARCHAR(36) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_confirmation CHANGE token token VARCHAR(64) NOT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
