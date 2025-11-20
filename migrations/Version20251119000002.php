<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to change tournament_status.id from VARCHAR to UUID
 */
final class Version20251119000002 extends AbstractMigration
{
    // Mapping of old string IDs to new UUIDs
    private const STATUS_UUID_MAP = [
        'created' => 'a50e8400-e29b-41d4-a716-446655440001',
        'active' => 'a50e8400-e29b-41d4-a716-446655440002',
        'deleted' => 'a50e8400-e29b-41d4-a716-446655440003',
        'archived' => 'a50e8400-e29b-41d4-a716-446655440004',
        'finalized' => 'a50e8400-e29b-41d4-a716-446655440005',
        'suspended' => 'a50e8400-e29b-41d4-a716-446655440006',
    ];

    public function getDescription(): string
    {
        return 'Convert tournament_status.id from VARCHAR to UUID and update all references';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Add temporary column to tournament_status for new UUID values
        $this->addSql('ALTER TABLE tournament_status ADD COLUMN id_new VARCHAR(36) NOT NULL DEFAULT ""');

        // Step 2: Update tournament_status with new UUID values
        foreach (self::STATUS_UUID_MAP as $oldId => $newUuid) {
            $this->addSql("UPDATE tournament_status SET id_new = '$newUuid' WHERE id = '$oldId'");
        }

        // Step 3: Add temporary column to tournament table
        $this->addSql('ALTER TABLE tournament ADD COLUMN tournament_status_id_new VARCHAR(36) DEFAULT NULL');

        // Step 4: Update tournament records to use new UUIDs based on mapping
        foreach (self::STATUS_UUID_MAP as $oldId => $newUuid) {
            $this->addSql("UPDATE tournament SET tournament_status_id_new = '$newUuid' WHERE tournament_status_id = '$oldId'");
        }

        // Step 5: Drop the old foreign key constraint
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9BB77A8AC');

        // Step 6: Drop the old primary key and rename columns in tournament_status
        $this->addSql('ALTER TABLE tournament_status DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE tournament_status DROP COLUMN id');
        $this->addSql('ALTER TABLE tournament_status CHANGE id_new id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE tournament_status ADD PRIMARY KEY (id)');

        // Step 7: Update tournament table - drop old column and rename new one
        $this->addSql('ALTER TABLE tournament DROP COLUMN tournament_status_id');
        $this->addSql('ALTER TABLE tournament CHANGE tournament_status_id_new tournament_status_id VARCHAR(36) NOT NULL');

        // Step 8: Re-create the foreign key constraint
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9BB77A8AC FOREIGN KEY (tournament_status_id) REFERENCES tournament_status (id)');

        // Step 9: Re-create the index that was dropped with the foreign key
        $this->addSql('CREATE INDEX IDX_BD5FB8D9BB77A8AC ON tournament (tournament_status_id)');
    }

    public function down(Schema $schema): void
    {
        // Reverse the process: convert UUIDs back to string IDs

        // Step 1: Drop the foreign key
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9BB77A8AC');

        // Step 2: Add temporary column to tournament_status for old string values
        $this->addSql('ALTER TABLE tournament_status ADD COLUMN id_old VARCHAR(36) NOT NULL DEFAULT ""');

        // Step 3: Update tournament_status with old string values
        foreach (self::STATUS_UUID_MAP as $oldId => $newUuid) {
            $this->addSql("UPDATE tournament_status SET id_old = '$oldId' WHERE id = '$newUuid'");
        }

        // Step 4: Add temporary column to tournament table
        $this->addSql('ALTER TABLE tournament ADD COLUMN tournament_status_id_old VARCHAR(36) DEFAULT NULL');

        // Step 5: Update tournament records to use old string IDs
        foreach (self::STATUS_UUID_MAP as $oldId => $newUuid) {
            $this->addSql("UPDATE tournament SET tournament_status_id_old = '$oldId' WHERE tournament_status_id = '$newUuid'");
        }

        // Step 6: Drop the old primary key and rename columns in tournament_status
        $this->addSql('ALTER TABLE tournament_status DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE tournament_status DROP COLUMN id');
        $this->addSql('ALTER TABLE tournament_status CHANGE id_old id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE tournament_status ADD PRIMARY KEY (id)');

        // Step 7: Update tournament table - drop UUID column and rename old one
        $this->addSql('ALTER TABLE tournament DROP COLUMN tournament_status_id');
        $this->addSql('ALTER TABLE tournament CHANGE tournament_status_id_old tournament_status_id VARCHAR(36) NOT NULL');

        // Step 8: Re-create the foreign key constraint
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9BB77A8AC FOREIGN KEY (tournament_status_id) REFERENCES tournament_status (id)');

        // Step 9: Re-create the index
        $this->addSql('CREATE INDEX IDX_BD5FB8D9BB77A8AC ON tournament (tournament_status_id)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
