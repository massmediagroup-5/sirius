<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161206133139 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE share_sizes_group_discount DROP FOREIGN KEY FK_4F9474047AF6A82');
        $this->addSql('ALTER TABLE share_sizes_group_discount DROP FOREIGN KEY FK_4F9474048227E3FD');
        $this->addSql('ALTER TABLE share_sizes_group_discount ADD CONSTRAINT FK_4F9474047AF6A82 FOREIGN KEY (share_group_id) REFERENCES share_sizes_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_discount ADD CONSTRAINT FK_4F9474048227E3FD FOREIGN KEY (companion_id) REFERENCES share_sizes_group (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE share_sizes_group_discount DROP FOREIGN KEY FK_4F9474047AF6A82');
        $this->addSql('ALTER TABLE share_sizes_group_discount DROP FOREIGN KEY FK_4F9474048227E3FD');
        $this->addSql('ALTER TABLE share_sizes_group_discount ADD CONSTRAINT FK_4F9474047AF6A82 FOREIGN KEY (share_group_id) REFERENCES share_sizes_group (id)');
        $this->addSql('ALTER TABLE share_sizes_group_discount ADD CONSTRAINT FK_4F9474048227E3FD FOREIGN KEY (companion_id) REFERENCES share_sizes_group (id)');
    }
}
