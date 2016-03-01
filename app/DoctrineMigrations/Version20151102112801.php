<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151102112801 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE credit (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, sku_products_id BIGINT DEFAULT NULL, status INT DEFAULT 0, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_call_back_users1_idx (users_id), INDEX fk_wish_list_sku_products1_idx (sku_products_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, status INT DEFAULT 0, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_call_back_users1_idx (users_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFE67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFEED2E3D59 FOREIGN KEY (sku_products_id) REFERENCES sku_products (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE credit');
        $this->addSql('DROP TABLE orders');
    }
}
