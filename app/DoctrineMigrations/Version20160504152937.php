<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160504152937 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX total_price ON orders');
        $this->addSql('ALTER TABLE orders ADD pay_status_id INT DEFAULT NULL, DROP total_price, DROP discounted_total_price');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE46F9BFC1 FOREIGN KEY (pay_status_id) REFERENCES order_status_pay (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE46F9BFC1 ON orders (pay_status_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE46F9BFC1');
        $this->addSql('DROP INDEX IDX_E52FFDEE46F9BFC1 ON orders');
        $this->addSql('ALTER TABLE orders ADD total_price NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, ADD discounted_total_price NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, DROP pay_status_id');
        $this->addSql('CREATE INDEX total_price ON orders (total_price)');
    }
}
