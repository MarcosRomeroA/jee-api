<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251215162540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournament ADD first_place_team_id VARCHAR(36) DEFAULT NULL, ADD second_place_team_id VARCHAR(36) DEFAULT NULL, ADD third_place_team_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D99ADD98D1 FOREIGN KEY (first_place_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D930584600 FOREIGN KEY (second_place_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D95AA89072 FOREIGN KEY (third_place_team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_BD5FB8D99ADD98D1 ON tournament (first_place_team_id)');
        $this->addSql('CREATE INDEX IDX_BD5FB8D930584600 ON tournament (second_place_team_id)');
        $this->addSql('CREATE INDEX IDX_BD5FB8D95AA89072 ON tournament (third_place_team_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D99ADD98D1');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D930584600');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D95AA89072');
        $this->addSql('DROP INDEX IDX_BD5FB8D99ADD98D1 ON tournament');
        $this->addSql('DROP INDEX IDX_BD5FB8D930584600 ON tournament');
        $this->addSql('DROP INDEX IDX_BD5FB8D95AA89072 ON tournament');
        $this->addSql('ALTER TABLE tournament DROP first_place_team_id, DROP second_place_team_id, DROP third_place_team_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
