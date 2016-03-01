<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160208124900 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE filters (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX text (name), UNIQUE INDEX ID_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filters_has_characteristics (filters_id INT NOT NULL, characteristics_id INT NOT NULL, INDEX IDX_4C49D9B86B715464 (filters_id), INDEX IDX_4C49D9B84B13ADB4 (characteristics_id), PRIMARY KEY(filters_id, characteristics_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE filters_has_characteristics ADD CONSTRAINT FK_4C49D9B86B715464 FOREIGN KEY (filters_id) REFERENCES filters (id)');
        $this->addSql('ALTER TABLE filters_has_characteristics ADD CONSTRAINT FK_4C49D9B84B13ADB4 FOREIGN KEY (characteristics_id) REFERENCES characteristics (id)');
        $this->addSql('DROP TABLE credit');
        $this->addSql('ALTER TABLE call_back CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE wishlist CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_model_images CHANGE link link VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', CHANGE thumbnail thumbnail VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, CHANGE priority priority INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE products_base_categories CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE cart CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE page_images CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE main_slider CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE menu_items CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX name ON menu_items (name)');
        $this->addSql('CREATE INDEX priority ON menu_items (priority)');
        $this->addSql('CREATE INDEX active ON menu_items (active)');
        $this->addSql('ALTER TABLE categories ADD filters_id INT DEFAULT NULL, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF346686B715464 FOREIGN KEY (filters_id) REFERENCES filters (id)');
        $this->addSql('CREATE INDEX fk_categories_filters1_idx ON categories (filters_id)');
        $this->addSql('CREATE INDEX param_name ON site_params (param_name)');
        $this->addSql('ALTER TABLE orders CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, CHANGE total_price total_price NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL');
        $this->addSql('CREATE INDEX total_price ON orders (total_price)');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_models CHANGE price price NUMERIC(8, 2) DEFAULT \'0.00\' NOT NULL, CHANGE oldprice oldprice NUMERIC(8, 2) DEFAULT NULL, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_images CHANGE link link VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', CHANGE thumbnail thumbnail VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_values CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, CHANGE In_filter In_filter TINYINT(1) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE vendors CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE sku_products CHANGE price price NUMERIC(8, 2) DEFAULT \'0.00\' NOT NULL, CHANGE quantity quantity INT DEFAULT 0 NOT NULL, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARBINARY(255) NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF346686B715464');
        $this->addSql('ALTER TABLE filters_has_characteristics DROP FOREIGN KEY FK_4C49D9B86B715464');
        $this->addSql('CREATE TABLE credit (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, product_models_id BIGINT DEFAULT NULL, status INT DEFAULT 0, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX fk_call_back_users1_idx (users_id), INDEX fk_credit_product_models1_idx (product_models_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFE67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFEB7C609C4 FOREIGN KEY (product_models_id) REFERENCES product_models (id)');
        $this->addSql('DROP TABLE filters');
        $this->addSql('DROP TABLE filters_has_characteristics');
        $this->addSql('ALTER TABLE call_back CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE cart CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX fk_categories_filters1_idx ON categories');
        $this->addSql('ALTER TABLE categories DROP filters_id, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristic_values CHANGE In_filter In_filter TINYINT(1) DEFAULT NULL, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE main_slider CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX name ON menu_items');
        $this->addSql('DROP INDEX priority ON menu_items');
        $this->addSql('DROP INDEX active ON menu_items');
        $this->addSql('ALTER TABLE menu_items CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX total_price ON orders');
        $this->addSql('ALTER TABLE orders CHANGE total_price total_price NUMERIC(10, 2) DEFAULT \'0.00\', CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE page_images CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_images CHANGE link link VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE thumbnail thumbnail VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_model_images CHANGE link link VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE thumbnail thumbnail VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE priority priority INT DEFAULT NULL, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_models CHANGE price price NUMERIC(8, 2) DEFAULT \'0.00\', CHANGE oldprice oldprice NUMERIC(8, 2) DEFAULT \'0.00\', CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE products_base_categories CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARBINARY(128) NOT NULL');
        $this->addSql('DROP INDEX param_name ON site_params');
        $this->addSql('ALTER TABLE sku_products CHANGE price price NUMERIC(8, 2) DEFAULT \'0.00\', CHANGE quantity quantity INT NOT NULL, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE vendors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE wishlist CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
