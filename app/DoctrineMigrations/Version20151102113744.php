<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151102113744 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A1C419F1F');
        $this->addSql('ALTER TABLE categories_has_characteristic_values DROP FOREIGN KEY FK_D25FA0CFA21214B7');
        $this->addSql('ALTER TABLE products_base_categories DROP FOREIGN KEY FK_E1A54CDFA21214B7');
        $this->addSql('ALTER TABLE products_has_categories DROP FOREIGN KEY FK_825E4F5CA21214B7');
        $this->addSql('ALTER TABLE characteristics DROP FOREIGN KEY FK_7037B15627A4A649');
        $this->addSql('ALTER TABLE categories_has_characteristic_values DROP FOREIGN KEY FK_D25FA0CFF7C3C4DC');
        $this->addSql('ALTER TABLE products_has_characteristic_values DROP FOREIGN KEY FK_8899C6F5F7C3C4DC');
        $this->addSql('ALTER TABLE characteristic_values DROP FOREIGN KEY FK_FCC77D084B13ADB4');
        $this->addSql('ALTER TABLE sku_products DROP FOREIGN KEY FK_30E7C15F5C002039');
        $this->addSql('ALTER TABLE page_images DROP FOREIGN KEY FK_8FC94874401ADD27');
        $this->addSql('ALTER TABLE product_images DROP FOREIGN KEY FK_8263FFCE6C8A81A9');
        $this->addSql('ALTER TABLE products_base_categories DROP FOREIGN KEY FK_E1A54CDF6C8A81A9');
        $this->addSql('ALTER TABLE products_has_categories DROP FOREIGN KEY FK_825E4F5C6C8A81A9');
        $this->addSql('ALTER TABLE products_has_characteristic_values DROP FOREIGN KEY FK_8899C6F56C8A81A9');
        $this->addSql('ALTER TABLE sku_products DROP FOREIGN KEY FK_30E7C15F6C8A81A9');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7ED2E3D59');
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY FK_1CC16EFEED2E3D59');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31ED2E3D59');
        $this->addSql('ALTER TABLE user_roles_has_users DROP FOREIGN KEY FK_B3F80FF9D84AB5C4');
        $this->addSql('ALTER TABLE call_back DROP FOREIGN KEY FK_5A4FA66B67B3B43D');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B767B3B43D');
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY FK_1CC16EFE67B3B43D');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE67B3B43D');
        $this->addSql('ALTER TABLE user_roles_has_users DROP FOREIGN KEY FK_B3F80FF967B3B43D');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A3167B3B43D');
        $this->addSql('ALTER TABLE sku_products DROP FOREIGN KEY FK_30E7C15F2E8B9E4D');
        $this->addSql('DROP TABLE action_labels');
        $this->addSql('DROP TABLE call_back');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE categories_has_characteristic_values');
        $this->addSql('DROP TABLE characteristic_groups');
        $this->addSql('DROP TABLE characteristic_values');
        $this->addSql('DROP TABLE characteristics');
        $this->addSql('DROP TABLE colors');
        $this->addSql('DROP TABLE credit');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE page_images');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE product_images');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE products_base_categories');
        $this->addSql('DROP TABLE products_has_categories');
        $this->addSql('DROP TABLE products_has_characteristic_values');
        $this->addSql('DROP TABLE site_params');
        $this->addSql('DROP TABLE sku_products');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE user_roles_has_users');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE vendors');
        $this->addSql('DROP TABLE wishlist');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE action_labels (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL COLLATE utf8_unicode_ci, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX name_UNIQUE (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE call_back (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, status INT DEFAULT 0, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX fk_call_back_users1_idx (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, sku_products_id BIGINT DEFAULT NULL, status INT DEFAULT 0, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX fk_call_back_users1_idx (users_id), INDEX fk_wish_list_sku_products1_idx (sku_products_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, alias VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, parrent INT DEFAULT NULL, in_menu TINYINT(1) DEFAULT NULL, active TINYINT(1) DEFAULT NULL, seo_title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, seo_description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, seo_keywords VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX alias_UNIQUE (alias), INDEX `index` (parrent), INDEX text (name), INDEX bool (active, in_menu), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories_has_characteristic_values (categories_id INT NOT NULL, characteristic_values_id INT NOT NULL, INDEX IDX_D25FA0CFA21214B7 (categories_id), INDEX IDX_D25FA0CFF7C3C4DC (characteristic_values_id), PRIMARY KEY(categories_id, characteristic_values_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristic_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX ID_UNIQUE (id), INDEX text (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristic_values (id INT AUTO_INCREMENT NOT NULL, characteristics_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX text (name), INDEX fk_characteristic_values_characteristics1_idx (characteristics_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristics (id INT AUTO_INCREMENT NOT NULL, characteristic_groups_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, In_filter TINYINT(1) DEFAULT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX text (name), INDEX fk_characteristics_characteristic_groups1_idx (characteristic_groups_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE colors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, hex VARCHAR(25) DEFAULT NULL COLLATE utf8_unicode_ci, priority INT DEFAULT NULL, description MEDIUMTEXT DEFAULT NULL COLLATE utf8_unicode_ci, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX index3 (priority), INDEX text (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE credit (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, sku_products_id BIGINT DEFAULT NULL, status INT DEFAULT 0, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX fk_call_back_users1_idx (users_id), INDEX fk_wish_list_sku_products1_idx (sku_products_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, status INT DEFAULT 0, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX fk_call_back_users1_idx (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_images (id INT AUTO_INCREMENT NOT NULL, pages_id INT DEFAULT NULL, link VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', thumbnail VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX fk_page_images_pages1_idx (pages_id), INDEX `index` (link, thumbnail, update_time, create_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, alias VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, description MEDIUMTEXT DEFAULT NULL COLLATE utf8_unicode_ci, content LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, seo_title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, seo_description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, seo_keywords VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX alias_UNIQUE (alias), INDEX `index` (update_time, create_time), INDEX text (title, alias, seo_title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_images (id INT AUTO_INCREMENT NOT NULL, products_id INT DEFAULT NULL, link VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', thumbnail VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX fk_images_products1_idx (products_id), INDEX index3 (thumbnail, link), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, action_labels_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, alias VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, description MEDIUMTEXT DEFAULT NULL COLLATE utf8_unicode_ci, price NUMERIC(8, 2) DEFAULT NULL, oldprice NUMERIC(8, 2) DEFAULT NULL, seo_title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, seo_description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, seo_keywords VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, status INT DEFAULT 0, active TINYINT(1) DEFAULT NULL, published TINYINT(1) DEFAULT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX alias_UNIQUE (alias), INDEX `index` (price, oldprice), INDEX text (name), INDEX fk_products_action_labels1_idx (action_labels_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_base_categories (id INT AUTO_INCREMENT NOT NULL, products_id INT DEFAULT NULL, categories_id INT DEFAULT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX products_id_UNIQUE (products_id), INDEX fk_product_base_categories_products1_idx (products_id), INDEX fk_product_base_categories_categories1_idx (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_has_categories (products_id INT NOT NULL, categories_id INT NOT NULL, INDEX IDX_825E4F5C6C8A81A9 (products_id), INDEX IDX_825E4F5CA21214B7 (categories_id), PRIMARY KEY(products_id, categories_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_has_characteristic_values (products_id INT NOT NULL, characteristic_values_id INT NOT NULL, INDEX IDX_8899C6F56C8A81A9 (products_id), INDEX IDX_8899C6F5F7C3C4DC (characteristic_values_id), PRIMARY KEY(products_id, characteristic_values_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site_params (id INT AUTO_INCREMENT NOT NULL, param_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, param_value VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sku_products (id BIGINT AUTO_INCREMENT NOT NULL, vendors_id INT DEFAULT NULL, colors_id INT DEFAULT NULL, products_id INT DEFAULT NULL, sku VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, price NUMERIC(8, 2) NOT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX fk_sku_vendors1_idx (vendors_id), INDEX fk_sku_products1_idx (products_id), INDEX `index` (price, sku), INDEX fk_sku_products_colors1_idx (colors_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX name_UNIQUE (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles_has_users (users_id INT NOT NULL, user_roles_id INT NOT NULL, INDEX IDX_B3F80FF967B3B43D (users_id), INDEX IDX_B3F80FF9D84AB5C4 (user_roles_id), PRIMARY KEY(users_id, user_roles_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci, email VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, password VARCHAR(32) NOT NULL COLLATE utf8_unicode_ci, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX username_UNIQUE (username), INDEX email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vendors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, priority INT DEFAULT NULL, description MEDIUMTEXT DEFAULT NULL COLLATE utf8_unicode_ci, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX index3 (priority), INDEX text (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, sku_products_id BIGINT DEFAULT NULL, status INT DEFAULT 0, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX fk_call_back_users1_idx (users_id), INDEX fk_wish_list_sku_products1_idx (sku_products_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE call_back ADD CONSTRAINT FK_5A4FA66B67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B767B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7ED2E3D59 FOREIGN KEY (sku_products_id) REFERENCES sku_products (id)');
        $this->addSql('ALTER TABLE categories_has_characteristic_values ADD CONSTRAINT FK_D25FA0CFA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE categories_has_characteristic_values ADD CONSTRAINT FK_D25FA0CFF7C3C4DC FOREIGN KEY (characteristic_values_id) REFERENCES characteristic_values (id)');
        $this->addSql('ALTER TABLE characteristic_values ADD CONSTRAINT FK_FCC77D084B13ADB4 FOREIGN KEY (characteristics_id) REFERENCES characteristics (id)');
        $this->addSql('ALTER TABLE characteristics ADD CONSTRAINT FK_7037B15627A4A649 FOREIGN KEY (characteristic_groups_id) REFERENCES characteristic_groups (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFE67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFEED2E3D59 FOREIGN KEY (sku_products_id) REFERENCES sku_products (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE page_images ADD CONSTRAINT FK_8FC94874401ADD27 FOREIGN KEY (pages_id) REFERENCES pages (id)');
        $this->addSql('ALTER TABLE product_images ADD CONSTRAINT FK_8263FFCE6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A1C419F1F FOREIGN KEY (action_labels_id) REFERENCES action_labels (id)');
        $this->addSql('ALTER TABLE products_base_categories ADD CONSTRAINT FK_E1A54CDF6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_base_categories ADD CONSTRAINT FK_E1A54CDFA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE products_has_categories ADD CONSTRAINT FK_825E4F5C6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_has_categories ADD CONSTRAINT FK_825E4F5CA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE products_has_characteristic_values ADD CONSTRAINT FK_8899C6F56C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_has_characteristic_values ADD CONSTRAINT FK_8899C6F5F7C3C4DC FOREIGN KEY (characteristic_values_id) REFERENCES characteristic_values (id)');
        $this->addSql('ALTER TABLE sku_products ADD CONSTRAINT FK_30E7C15F2E8B9E4D FOREIGN KEY (vendors_id) REFERENCES vendors (id)');
        $this->addSql('ALTER TABLE sku_products ADD CONSTRAINT FK_30E7C15F5C002039 FOREIGN KEY (colors_id) REFERENCES colors (id)');
        $this->addSql('ALTER TABLE sku_products ADD CONSTRAINT FK_30E7C15F6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE user_roles_has_users ADD CONSTRAINT FK_B3F80FF967B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_roles_has_users ADD CONSTRAINT FK_B3F80FF9D84AB5C4 FOREIGN KEY (user_roles_id) REFERENCES user_roles (id)');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A3167B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31ED2E3D59 FOREIGN KEY (sku_products_id) REFERENCES sku_products (id)');
    }
}
