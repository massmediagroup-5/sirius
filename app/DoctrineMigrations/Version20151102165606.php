<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151102165606 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE call_back (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, status INT DEFAULT 0, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_call_back_users1_idx (users_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX name_UNIQUE (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, product_models_id BIGINT DEFAULT NULL, status INT DEFAULT 0, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_call_back_users1_idx (users_id), INDEX fk_wishlist_product_models1_idx (product_models_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_base_categories (id INT AUTO_INCREMENT NOT NULL, categories_id INT DEFAULT NULL, products_id INT DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_product_base_categories_products1_idx (products_id), INDEX fk_product_base_categories_categories1_idx (categories_id), UNIQUE INDEX products_id_UNIQUE (products_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, orders_id INT DEFAULT NULL, product_models_id BIGINT DEFAULT NULL, status INT DEFAULT 0, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_call_back_users1_idx (users_id), INDEX fk_cart_orders1_idx (orders_id), INDEX fk_cart_product_models1_idx (product_models_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_images (id INT AUTO_INCREMENT NOT NULL, pages_id INT DEFAULT NULL, link VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', thumbnail VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_page_images_pages1_idx (pages_id), INDEX `index` (link, thumbnail, update_time, create_time), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE credit (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, product_models_id BIGINT DEFAULT NULL, status INT DEFAULT 0, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_call_back_users1_idx (users_id), INDEX fk_credit_product_models1_idx (product_models_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, alias VARCHAR(255) DEFAULT NULL, parrent INT DEFAULT NULL, in_menu TINYINT(1) DEFAULT NULL, active TINYINT(1) DEFAULT NULL, seo_title VARCHAR(255) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, seo_keywords VARCHAR(255) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX `index` (parrent), INDEX text (name), INDEX bool (active, in_menu), UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX alias_UNIQUE (alias), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories_has_characteristic_values (categories_id INT NOT NULL, characteristic_values_id INT NOT NULL, INDEX IDX_D25FA0CFA21214B7 (categories_id), INDEX IDX_D25FA0CFF7C3C4DC (characteristic_values_id), PRIMARY KEY(categories_id, characteristic_values_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_variants_values (id INT AUTO_INCREMENT NOT NULL, product_variants_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, value VARCHAR(25) DEFAULT NULL, priority INT DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX index3 (priority), INDEX text (name), INDEX fk_product_variants_values_product_variants1_idx (product_variants_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE action_labels (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, html_class VARCHAR(45) DEFAULT NULL, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX name_UNIQUE (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site_params (id INT AUTO_INCREMENT NOT NULL, param_name VARCHAR(255) DEFAULT NULL, param_value VARCHAR(255) DEFAULT NULL, UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, status INT DEFAULT 0, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_call_back_users1_idx (users_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, alias VARCHAR(255) DEFAULT NULL, description MEDIUMTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, seo_title VARCHAR(255) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, seo_keywords VARCHAR(255) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX `index` (update_time, create_time), INDEX text (title, alias, seo_title), UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX alias_UNIQUE (alias), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_variants (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, description MEDIUMTEXT DEFAULT NULL, INDEX text (name), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_models (id BIGINT AUTO_INCREMENT NOT NULL, product_variants_values_id INT DEFAULT NULL, products_id INT DEFAULT NULL, alias VARCHAR(255) DEFAULT NULL, price NUMERIC(8, 2) NOT NULL, oldprice NUMERIC(8, 2) DEFAULT NULL, seo_title VARCHAR(255) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, seo_keywords VARCHAR(255) DEFAULT NULL, status INT DEFAULT 0, active TINYINT(1) DEFAULT NULL, published TINYINT(1) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX `index` (price), INDEX fk_product_models_products1_idx (products_id), INDEX fk_product_models_product_variants_values1_idx (product_variants_values_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_images (id INT AUTO_INCREMENT NOT NULL, product_models_id BIGINT DEFAULT NULL, link VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', thumbnail VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX index3 (thumbnail, link), INDEX fk_product_images_product_models1_idx (product_models_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(50) NOT NULL, email VARCHAR(255) DEFAULT NULL, password VARCHAR(32) NOT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX email (email), UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX username_UNIQUE (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles_has_users (users_id INT NOT NULL, user_roles_id INT NOT NULL, INDEX IDX_B3F80FF967B3B43D (users_id), INDEX IDX_B3F80FF9D84AB5C4 (user_roles_id), PRIMARY KEY(users_id, user_roles_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristic_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX text (name), UNIQUE INDEX ID_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristic_values (id INT AUTO_INCREMENT NOT NULL, characteristics_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX text (name), INDEX fk_characteristic_values_characteristics1_idx (characteristics_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vendors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, priority INT DEFAULT NULL, description MEDIUMTEXT DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX index3 (priority), INDEX text (name), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sku_products (id BIGINT AUTO_INCREMENT NOT NULL, product_models_id BIGINT DEFAULT NULL, vendors_id INT DEFAULT NULL, sku VARCHAR(255) NOT NULL, price NUMERIC(8, 2) NOT NULL, count INT NOT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX `index` (price, sku), INDEX fk_sku_products_product_models1_idx (product_models_id), INDEX fk_sku_products_vendors1_idx (vendors_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristics (id INT AUTO_INCREMENT NOT NULL, characteristic_groups_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, In_filter TINYINT(1) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX text (name), INDEX fk_characteristics_characteristic_groups1_idx (characteristic_groups_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, action_labels_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, status INT DEFAULT 0, active TINYINT(1) DEFAULT NULL, published TINYINT(1) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX text (name), INDEX fk_products_action_labels1_idx (action_labels_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_has_categories (products_id INT NOT NULL, categories_id INT NOT NULL, INDEX IDX_825E4F5C6C8A81A9 (products_id), INDEX IDX_825E4F5CA21214B7 (categories_id), PRIMARY KEY(products_id, categories_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_has_characteristic_values (products_id INT NOT NULL, characteristic_values_id INT NOT NULL, INDEX IDX_8899C6F56C8A81A9 (products_id), INDEX IDX_8899C6F5F7C3C4DC (characteristic_values_id), PRIMARY KEY(products_id, characteristic_values_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE call_back ADD CONSTRAINT FK_5A4FA66B67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A3167B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31B7C609C4 FOREIGN KEY (product_models_id) REFERENCES product_models (id)');
        $this->addSql('ALTER TABLE products_base_categories ADD CONSTRAINT FK_E1A54CDFA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE products_base_categories ADD CONSTRAINT FK_E1A54CDF6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B767B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7B7C609C4 FOREIGN KEY (product_models_id) REFERENCES product_models (id)');
        $this->addSql('ALTER TABLE page_images ADD CONSTRAINT FK_8FC94874401ADD27 FOREIGN KEY (pages_id) REFERENCES pages (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFE67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFEB7C609C4 FOREIGN KEY (product_models_id) REFERENCES product_models (id)');
        $this->addSql('ALTER TABLE categories_has_characteristic_values ADD CONSTRAINT FK_D25FA0CFA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE categories_has_characteristic_values ADD CONSTRAINT FK_D25FA0CFF7C3C4DC FOREIGN KEY (characteristic_values_id) REFERENCES characteristic_values (id)');
        $this->addSql('ALTER TABLE product_variants_values ADD CONSTRAINT FK_3063F4A7D0CF4EA FOREIGN KEY (product_variants_id) REFERENCES product_variants (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE product_models ADD CONSTRAINT FK_86AA71AD1E1D9ABE FOREIGN KEY (product_variants_values_id) REFERENCES product_variants_values (id)');
        $this->addSql('ALTER TABLE product_models ADD CONSTRAINT FK_86AA71AD6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE product_images ADD CONSTRAINT FK_8263FFCEB7C609C4 FOREIGN KEY (product_models_id) REFERENCES product_models (id)');
        $this->addSql('ALTER TABLE user_roles_has_users ADD CONSTRAINT FK_B3F80FF967B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_roles_has_users ADD CONSTRAINT FK_B3F80FF9D84AB5C4 FOREIGN KEY (user_roles_id) REFERENCES user_roles (id)');
        $this->addSql('ALTER TABLE characteristic_values ADD CONSTRAINT FK_FCC77D084B13ADB4 FOREIGN KEY (characteristics_id) REFERENCES characteristics (id)');
        $this->addSql('ALTER TABLE sku_products ADD CONSTRAINT FK_30E7C15FB7C609C4 FOREIGN KEY (product_models_id) REFERENCES product_models (id)');
        $this->addSql('ALTER TABLE sku_products ADD CONSTRAINT FK_30E7C15F2E8B9E4D FOREIGN KEY (vendors_id) REFERENCES vendors (id)');
        $this->addSql('ALTER TABLE characteristics ADD CONSTRAINT FK_7037B15627A4A649 FOREIGN KEY (characteristic_groups_id) REFERENCES characteristic_groups (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A1C419F1F FOREIGN KEY (action_labels_id) REFERENCES action_labels (id)');
        $this->addSql('ALTER TABLE products_has_categories ADD CONSTRAINT FK_825E4F5C6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_has_categories ADD CONSTRAINT FK_825E4F5CA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE products_has_characteristic_values ADD CONSTRAINT FK_8899C6F56C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_has_characteristic_values ADD CONSTRAINT FK_8899C6F5F7C3C4DC FOREIGN KEY (characteristic_values_id) REFERENCES characteristic_values (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_roles_has_users DROP FOREIGN KEY FK_B3F80FF9D84AB5C4');
        $this->addSql('ALTER TABLE products_base_categories DROP FOREIGN KEY FK_E1A54CDFA21214B7');
        $this->addSql('ALTER TABLE categories_has_characteristic_values DROP FOREIGN KEY FK_D25FA0CFA21214B7');
        $this->addSql('ALTER TABLE products_has_categories DROP FOREIGN KEY FK_825E4F5CA21214B7');
        $this->addSql('ALTER TABLE product_models DROP FOREIGN KEY FK_86AA71AD1E1D9ABE');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A1C419F1F');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7CFFE9AD6');
        $this->addSql('ALTER TABLE page_images DROP FOREIGN KEY FK_8FC94874401ADD27');
        $this->addSql('ALTER TABLE product_variants_values DROP FOREIGN KEY FK_3063F4A7D0CF4EA');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31B7C609C4');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7B7C609C4');
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY FK_1CC16EFEB7C609C4');
        $this->addSql('ALTER TABLE product_images DROP FOREIGN KEY FK_8263FFCEB7C609C4');
        $this->addSql('ALTER TABLE sku_products DROP FOREIGN KEY FK_30E7C15FB7C609C4');
        $this->addSql('ALTER TABLE call_back DROP FOREIGN KEY FK_5A4FA66B67B3B43D');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A3167B3B43D');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B767B3B43D');
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY FK_1CC16EFE67B3B43D');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE67B3B43D');
        $this->addSql('ALTER TABLE user_roles_has_users DROP FOREIGN KEY FK_B3F80FF967B3B43D');
        $this->addSql('ALTER TABLE characteristics DROP FOREIGN KEY FK_7037B15627A4A649');
        $this->addSql('ALTER TABLE categories_has_characteristic_values DROP FOREIGN KEY FK_D25FA0CFF7C3C4DC');
        $this->addSql('ALTER TABLE products_has_characteristic_values DROP FOREIGN KEY FK_8899C6F5F7C3C4DC');
        $this->addSql('ALTER TABLE sku_products DROP FOREIGN KEY FK_30E7C15F2E8B9E4D');
        $this->addSql('ALTER TABLE characteristic_values DROP FOREIGN KEY FK_FCC77D084B13ADB4');
        $this->addSql('ALTER TABLE products_base_categories DROP FOREIGN KEY FK_E1A54CDF6C8A81A9');
        $this->addSql('ALTER TABLE product_models DROP FOREIGN KEY FK_86AA71AD6C8A81A9');
        $this->addSql('ALTER TABLE products_has_categories DROP FOREIGN KEY FK_825E4F5C6C8A81A9');
        $this->addSql('ALTER TABLE products_has_characteristic_values DROP FOREIGN KEY FK_8899C6F56C8A81A9');
        $this->addSql('DROP TABLE call_back');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE wishlist');
        $this->addSql('DROP TABLE products_base_categories');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE page_images');
        $this->addSql('DROP TABLE credit');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE categories_has_characteristic_values');
        $this->addSql('DROP TABLE product_variants_values');
        $this->addSql('DROP TABLE action_labels');
        $this->addSql('DROP TABLE site_params');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE product_variants');
        $this->addSql('DROP TABLE product_models');
        $this->addSql('DROP TABLE product_images');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_roles_has_users');
        $this->addSql('DROP TABLE characteristic_groups');
        $this->addSql('DROP TABLE characteristic_values');
        $this->addSql('DROP TABLE vendors');
        $this->addSql('DROP TABLE sku_products');
        $this->addSql('DROP TABLE characteristics');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE products_has_categories');
        $this->addSql('DROP TABLE products_has_characteristic_values');
    }
}
