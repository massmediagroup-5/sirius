<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151208180253 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE call_back CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE wishlist CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX index3 ON product_model_images');
        $this->addSql('ALTER TABLE product_model_images CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX link ON product_model_images (link)');
        $this->addSql('CREATE INDEX thumbnail ON product_model_images (thumbnail)');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX hex ON product_colors (hex)');
        $this->addSql('DROP INDEX text ON product_colors');
        $this->addSql('CREATE INDEX name ON product_colors (name)');
        $this->addSql('ALTER TABLE products_base_categories CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE cart CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE page_images CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE credit CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE main_slider CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE menu_items CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX bool ON categories');
        $this->addSql('ALTER TABLE categories CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX in_menu ON categories (in_menu)');
        $this->addSql('CREATE INDEX active ON categories (active)');
        $this->addSql('ALTER TABLE orders CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_priorities CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_models CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX price ON product_models (price)');
        $this->addSql('CREATE INDEX oldprice ON product_models (oldprice)');
        $this->addSql('CREATE UNIQUE INDEX alias_UNIQUE ON product_models (alias)');
        $this->addSql('ALTER TABLE product_images CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_values ADD In_filter TINYINT(1) DEFAULT NULL, ADD not_parse TINYINT(1) DEFAULT \'0\', CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX in_filter ON characteristic_values (In_filter)');
        $this->addSql('CREATE INDEX not_parse ON characteristic_values (not_parse)');
        $this->addSql('ALTER TABLE vendors CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX index3 ON vendors');
        $this->addSql('CREATE INDEX priority ON vendors (priority)');
        $this->addSql('DROP INDEX text ON vendors');
        $this->addSql('CREATE INDEX name ON vendors (name)');
        $this->addSql('ALTER TABLE sku_products CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX in_filter ON characteristics (In_filter)');
        $this->addSql('DROP INDEX text ON characteristics');
        $this->addSql('CREATE INDEX name ON characteristics (name)');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX status ON products (status)');
        $this->addSql('CREATE INDEX active ON products (active)');
        $this->addSql('CREATE INDEX published ON products (published)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE call_back CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE cart CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX in_menu ON categories');
        $this->addSql('DROP INDEX active ON categories');
        $this->addSql('ALTER TABLE categories CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX bool ON categories (active, in_menu)');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristic_priorities CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX in_filter ON characteristic_values');
        $this->addSql('DROP INDEX not_parse ON characteristic_values');
        $this->addSql('ALTER TABLE characteristic_values DROP In_filter, DROP not_parse, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX in_filter ON characteristics');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX name ON characteristics');
        $this->addSql('CREATE INDEX text ON characteristics (name)');
        $this->addSql('ALTER TABLE credit CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE main_slider CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE menu_items CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE orders CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE page_images CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX hex ON product_colors');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX name ON product_colors');
        $this->addSql('CREATE INDEX text ON product_colors (name)');
        $this->addSql('ALTER TABLE product_images CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX link ON product_model_images');
        $this->addSql('DROP INDEX thumbnail ON product_model_images');
        $this->addSql('ALTER TABLE product_model_images CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX index3 ON product_model_images (thumbnail, link)');
        $this->addSql('DROP INDEX price ON product_models');
        $this->addSql('DROP INDEX oldprice ON product_models');
        $this->addSql('DROP INDEX alias_UNIQUE ON product_models');
        $this->addSql('ALTER TABLE product_models CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX status ON products');
        $this->addSql('DROP INDEX active ON products');
        $this->addSql('DROP INDEX published ON products');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE products_base_categories CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE sku_products CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE vendors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX priority ON vendors');
        $this->addSql('CREATE INDEX index3 ON vendors (priority)');
        $this->addSql('DROP INDEX name ON vendors');
        $this->addSql('CREATE INDEX text ON vendors (name)');
        $this->addSql('ALTER TABLE wishlist CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
