<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151021181524 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE call_back (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, alias VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, status INT DEFAULT 0, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX fk_call_back_users1_idx (users_id), UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX alias_UNIQUE (alias), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX name_UNIQUE (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, description MEDIUMTEXT DEFAULT NULL, INDEX `index` (name), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_images (id INT AUTO_INCREMENT NOT NULL, pages_id INT DEFAULT NULL, link VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', thumbnail VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX fk_page_images_pages1_idx (pages_id), INDEX `index` (link, thumbnail, update_time, create_time), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_items (id INT AUTO_INCREMENT NOT NULL, menu_id INT DEFAULT NULL, name VARCHAR(45) DEFAULT NULL, priority TINYINT(1) DEFAULT NULL, link VARCHAR(190) NOT NULL, link_type VARCHAR(8) DEFAULT \'local\', create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_menu_items_menu1_idx (menu_id), INDEX link (link), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, alias VARCHAR(255) DEFAULT NULL, parrent INT DEFAULT NULL, status INT DEFAULT 0, active TINYINT(1) DEFAULT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX `index` (parrent), INDEX text (name), UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX alias_UNIQUE (alias), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site_params (id INT AUTO_INCREMENT NOT NULL, param_name VARCHAR(255) DEFAULT NULL, param_value VARCHAR(255) DEFAULT NULL, UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, alias VARCHAR(255) DEFAULT NULL, description MEDIUMTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, seo_title VARCHAR(45) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, seo_keywords VARCHAR(255) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX `index` (update_time, create_time), INDEX text (title, alias, seo_title), UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX alias_UNIQUE (alias), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filters (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX text (name), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_images (id INT AUTO_INCREMENT NOT NULL, products_id INT DEFAULT NULL, link VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', thumbnail VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX fk_images_products1_idx (products_id), INDEX index3 (thumbnail, link), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(50) NOT NULL, email VARCHAR(255) DEFAULT NULL, password VARCHAR(32) NOT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX email (email), UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX username_UNIQUE (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles_has_users (users_id INT NOT NULL, user_roles_id INT NOT NULL, INDEX IDX_B3F80FF967B3B43D (users_id), INDEX IDX_B3F80FF9D84AB5C4 (user_roles_id), PRIMARY KEY(users_id, user_roles_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vendors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, priority INT DEFAULT NULL, description MEDIUMTEXT DEFAULT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX index3 (priority), INDEX text (name), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sku_products (id BIGINT AUTO_INCREMENT NOT NULL, vendors_id INT DEFAULT NULL, products_id INT DEFAULT NULL, sku VARCHAR(255) NOT NULL, price NUMERIC(8, 2) NOT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX fk_sku_vendors1_idx (vendors_id), INDEX fk_sku_products1_idx (products_id), INDEX `index` (price, sku), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristics (id INT AUTO_INCREMENT NOT NULL, filters_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX text (name), INDEX fk_characteristics_filters1_idx (filters_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filter_sets (id INT AUTO_INCREMENT NOT NULL, filters_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX fk_filters_sets_filters1_idx (filters_id), INDEX text (name), UNIQUE INDEX ID_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, alias VARCHAR(255) DEFAULT NULL, description MEDIUMTEXT DEFAULT NULL, price NUMERIC(8, 2) DEFAULT NULL, oldprice NUMERIC(8, 2) DEFAULT NULL, seo_title VARCHAR(45) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, seo_keywords VARCHAR(255) DEFAULT NULL, status INT DEFAULT 0, active TINYINT(1) DEFAULT NULL, published TINYINT(1) DEFAULT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX fk_product_category_idx (category_id), INDEX `index` (price, oldprice), INDEX text (name), UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX alias_UNIQUE (alias), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_has_filter_sets (products_id INT NOT NULL, filters_sets_id INT NOT NULL, INDEX IDX_DDBB7B146C8A81A9 (products_id), INDEX IDX_DDBB7B14D9EF4B14 (filters_sets_id), PRIMARY KEY(products_id, filters_sets_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE call_back ADD CONSTRAINT FK_5A4FA66B67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE page_images ADD CONSTRAINT FK_8FC94874401ADD27 FOREIGN KEY (pages_id) REFERENCES pages (id)');
        $this->addSql('ALTER TABLE menu_items ADD CONSTRAINT FK_70B2CA2ACCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE product_images ADD CONSTRAINT FK_8263FFCE6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE user_roles_has_users ADD CONSTRAINT FK_B3F80FF967B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_roles_has_users ADD CONSTRAINT FK_B3F80FF9D84AB5C4 FOREIGN KEY (user_roles_id) REFERENCES user_roles (id)');
        $this->addSql('ALTER TABLE sku_products ADD CONSTRAINT FK_30E7C15F2E8B9E4D FOREIGN KEY (vendors_id) REFERENCES vendors (id)');
        $this->addSql('ALTER TABLE sku_products ADD CONSTRAINT FK_30E7C15F6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE characteristics ADD CONSTRAINT FK_7037B1566B715464 FOREIGN KEY (filters_id) REFERENCES filters (id)');
        $this->addSql('ALTER TABLE filter_sets ADD CONSTRAINT FK_FCB722A86B715464 FOREIGN KEY (filters_id) REFERENCES filters (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE products_has_filter_sets ADD CONSTRAINT FK_DDBB7B146C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_has_filter_sets ADD CONSTRAINT FK_DDBB7B14D9EF4B14 FOREIGN KEY (filters_sets_id) REFERENCES filter_sets (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_roles_has_users DROP FOREIGN KEY FK_B3F80FF9D84AB5C4');
        $this->addSql('ALTER TABLE menu_items DROP FOREIGN KEY FK_70B2CA2ACCD7E912');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A12469DE2');
        $this->addSql('ALTER TABLE page_images DROP FOREIGN KEY FK_8FC94874401ADD27');
        $this->addSql('ALTER TABLE characteristics DROP FOREIGN KEY FK_7037B1566B715464');
        $this->addSql('ALTER TABLE filter_sets DROP FOREIGN KEY FK_FCB722A86B715464');
        $this->addSql('ALTER TABLE call_back DROP FOREIGN KEY FK_5A4FA66B67B3B43D');
        $this->addSql('ALTER TABLE user_roles_has_users DROP FOREIGN KEY FK_B3F80FF967B3B43D');
        $this->addSql('ALTER TABLE sku_products DROP FOREIGN KEY FK_30E7C15F2E8B9E4D');
        $this->addSql('ALTER TABLE products_has_filter_sets DROP FOREIGN KEY FK_DDBB7B14D9EF4B14');
        $this->addSql('ALTER TABLE product_images DROP FOREIGN KEY FK_8263FFCE6C8A81A9');
        $this->addSql('ALTER TABLE sku_products DROP FOREIGN KEY FK_30E7C15F6C8A81A9');
        $this->addSql('ALTER TABLE products_has_filter_sets DROP FOREIGN KEY FK_DDBB7B146C8A81A9');
        $this->addSql('DROP TABLE call_back');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE page_images');
        $this->addSql('DROP TABLE menu_items');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE site_params');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE filters');
        $this->addSql('DROP TABLE product_images');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_roles_has_users');
        $this->addSql('DROP TABLE vendors');
        $this->addSql('DROP TABLE sku_products');
        $this->addSql('DROP TABLE characteristics');
        $this->addSql('DROP TABLE filter_sets');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE products_has_filter_sets');
    }
}
