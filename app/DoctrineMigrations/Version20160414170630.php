<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160414170630 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7ED2E3D59');
        $this->addSql('DROP TABLE sku_products');
        $this->addSql('ALTER TABLE product_images CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX name ON product_models');
        $this->addSql('ALTER TABLE product_models DROP name, DROP seo_title, DROP seo_description, DROP seo_keywords, DROP description, DROP content, DROP characteristics, DROP features, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX fk_cart_sku_products1_idx ON cart');
        $this->addSql('ALTER TABLE cart DROP sku_products_id, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_values CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARBINARY(255) NOT NULL');
        $this->addSql('ALTER TABLE menu_items CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX name ON products');
        $this->addSql('ALTER TABLE products ADD article VARCHAR(255) NOT NULL, ADD seo_title VARCHAR(255) DEFAULT NULL, ADD seo_description VARCHAR(255) DEFAULT NULL, ADD seo_keywords VARCHAR(255) DEFAULT NULL, ADD content LONGTEXT DEFAULT NULL, ADD characteristics LONGTEXT DEFAULT NULL, ADD features LONGTEXT DEFAULT NULL, ADD price NUMERIC(8, 2) DEFAULT \'0\', ADD wholesale_price NUMERIC(8, 2) DEFAULT \'0\' NOT NULL, ADD quantity INT DEFAULT 0 NOT NULL, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, CHANGE import_name name VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sku_products (id BIGINT AUTO_INCREMENT NOT NULL, vendors_id INT DEFAULT NULL, product_models_id BIGINT DEFAULT NULL, sku VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, price NUMERIC(8, 2) DEFAULT \'0.00\', quantity INT DEFAULT 0 NOT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, status TINYINT(1) DEFAULT \'0\' NOT NULL, active TINYINT(1) DEFAULT NULL, wholesale_price NUMERIC(8, 2) DEFAULT \'0.00\', UNIQUE INDEX id_UNIQUE (id), INDEX `index` (price, sku), INDEX fk_sku_products_product_models1_idx (product_models_id), INDEX fk_sku_products_vendors1_idx (vendors_id), INDEX name (name), INDEX count (quantity), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sku_products ADD CONSTRAINT FK_30E7C15F2E8B9E4D FOREIGN KEY (vendors_id) REFERENCES vendors (id)');
        $this->addSql('ALTER TABLE sku_products ADD CONSTRAINT FK_30E7C15FB7C609C4 FOREIGN KEY (product_models_id) REFERENCES product_models (id)');
        $this->addSql('ALTER TABLE call_back CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE cart ADD sku_products_id BIGINT DEFAULT NULL, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7ED2E3D59 FOREIGN KEY (sku_products_id) REFERENCES sku_products (id)');
        $this->addSql('CREATE INDEX fk_cart_sku_products1_idx ON cart (sku_products_id)');
        $this->addSql('ALTER TABLE product_models ADD name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD seo_title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD seo_description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD seo_keywords VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD description MEDIUMTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD content LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD characteristics LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD features LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX name ON product_models (name)');
        $this->addSql('ALTER TABLE products ADD import_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP name, DROP article, DROP seo_title, DROP seo_description, DROP seo_keywords, DROP content, DROP characteristics, DROP features, DROP price, DROP wholesale_price, DROP quantity, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX name ON products (import_name)');
    }
}
