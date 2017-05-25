<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170524134154 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE novaposhta_sender (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, warehouse_id INT DEFAULT NULL, name VARCHAR(128) DEFAULT NULL, INDEX IDX_B6626BDC8BAC62AF (city_id), INDEX IDX_B6626BDC5080ECDE (warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE novaposhta_sender ADD CONSTRAINT FK_B6626BDC8BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE novaposhta_sender ADD CONSTRAINT FK_B6626BDC5080ECDE FOREIGN KEY (warehouse_id) REFERENCES stores (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE novaposhta_sender');
    }
}
