<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251115000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change post_like.id column from INT AUTO_INCREMENT to VARCHAR(36) for UUID support';
    }

    public function up(Schema $schema): void
    {
        // Drop foreign key constraints that reference post_like
        $this->addSql('ALTER TABLE post_like DROP FOREIGN KEY FK_653627B8A76ED395');
        $this->addSql('ALTER TABLE post_like DROP FOREIGN KEY FK_653627B84B89032C');

        // Drop the primary key
        $this->addSql('ALTER TABLE post_like MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE post_like DROP PRIMARY KEY');

        // Change the id column type from INT AUTO_INCREMENT to VARCHAR(36)
        $this->addSql('ALTER TABLE post_like MODIFY id VARCHAR(36) NOT NULL');

        // Re-add the primary key
        $this->addSql('ALTER TABLE post_like ADD PRIMARY KEY (id)');

        // Recreate foreign key constraints
        $this->addSql('ALTER TABLE post_like ADD CONSTRAINT FK_653627B8A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE post_like ADD CONSTRAINT FK_653627B84B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign key constraints
        $this->addSql('ALTER TABLE post_like DROP FOREIGN KEY FK_653627B8A76ED395');
        $this->addSql('ALTER TABLE post_like DROP FOREIGN KEY FK_653627B84B89032C');

        // Drop the primary key
        $this->addSql('ALTER TABLE post_like DROP PRIMARY KEY');

        // Revert the id column type from VARCHAR(36) to INT AUTO_INCREMENT
        $this->addSql('ALTER TABLE post_like MODIFY id INT AUTO_INCREMENT NOT NULL');

        // Re-add the primary key
        $this->addSql('ALTER TABLE post_like ADD PRIMARY KEY (id)');

        // Recreate foreign key constraints
        $this->addSql('ALTER TABLE post_like ADD CONSTRAINT FK_653627B8A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE post_like ADD CONSTRAINT FK_653627B84B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
    }
}
