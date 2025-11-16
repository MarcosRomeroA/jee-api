<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251113070000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert tournament statuses into tournament_status table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO `tournament_status` (`id`, `name`) VALUES
            ('01234567-89ab-cdef-0123-000000000001', 'created'),
            ('01234567-89ab-cdef-0123-000000000002', 'active'),
            ('01234567-89ab-cdef-0123-000000000003', 'deleted'),
            ('01234567-89ab-cdef-0123-000000000004', 'archived'),
            ('01234567-89ab-cdef-0123-000000000005', 'finalized'),
            ('01234567-89ab-cdef-0123-000000000006', 'suspended')
        ON DUPLICATE KEY UPDATE `name` = VALUES(`name`)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM `tournament_status` WHERE `id` IN (
            '01234567-89ab-cdef-0123-000000000001',
            '01234567-89ab-cdef-0123-000000000002',
            '01234567-89ab-cdef-0123-000000000003',
            '01234567-89ab-cdef-0123-000000000004',
            '01234567-89ab-cdef-0123-000000000005',
            '01234567-89ab-cdef-0123-000000000006'
        )");
    }
}
