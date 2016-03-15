<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160316151405 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX index3 ON product_images');
        $this->addSql('ALTER TABLE product_images DROP thumbnail, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX index3 ON product_images (link)');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_models CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE sku_products CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE cart CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_values CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARBINARY(255) NOT NULL');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX `index` ON page_images');
        $this->addSql('ALTER TABLE page_images DROP thumbnail, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX `index` ON page_images (link, update_time, create_time)');
        $this->addSql('ALTER TABLE filters CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE menu_items CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX thumbnail ON product_model_images');
        $this->addSql('ALTER TABLE product_model_images DROP thumbnail, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_model_decoration_colors CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE orders CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE categories ADD content LONGTEXT DEFAULT NULL, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE main_slider CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE wishlist CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE vendors CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE call_back CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
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
        $this->addSql('ALTER TABLE categories DROP content, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristic_values CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE filters CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE main_slider CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE menu_items CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE orders CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX `index` ON page_images');
        $this->addSql('ALTER TABLE page_images ADD thumbnail VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX `index` ON page_images (link, thumbnail, update_time, create_time)');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX index3 ON product_images');
        $this->addSql('ALTER TABLE product_images ADD thumbnail VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX index3 ON product_images (thumbnail, link)');
        $this->addSql('ALTER TABLE product_model_decoration_colors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_model_images ADD thumbnail VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX thumbnail ON product_model_images (thumbnail)');
        $this->addSql('ALTER TABLE product_models CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARBINARY(255) NOT NULL');
        $this->addSql('ALTER TABLE sku_products CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE vendors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE wishlist CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
