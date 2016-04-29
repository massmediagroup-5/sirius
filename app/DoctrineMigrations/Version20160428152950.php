<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160428152950 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders ADD related_order_id INT DEFAULT NULL, ADD pre_order_flag TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE2B1C2395 FOREIGN KEY (related_order_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE2B1C2395 ON orders (related_order_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE2B1C2395');
        $this->addSql('DROP INDEX IDX_E52FFDEE2B1C2395 ON orders');
        $this->addSql('ALTER TABLE orders DROP related_order_id, DROP pre_order_flag');
    }
}
