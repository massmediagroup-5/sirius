<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151029124028 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, sku_products_id BIGINT DEFAULT NULL, status INT DEFAULT 0, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_call_back_users1_idx (users_id), INDEX fk_wish_list_sku_products1_idx (sku_products_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE colors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, hex VARCHAR(25) DEFAULT NULL, priority INT DEFAULT NULL, description MEDIUMTEXT DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX index3 (priority), INDEX text (name), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_base_categories (id INT AUTO_INCREMENT NOT NULL, categories_id INT DEFAULT NULL, products_id INT DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_product_base_categories_products1_idx (products_id), INDEX fk_product_base_categories_categories1_idx (categories_id), UNIQUE INDEX products_id_UNIQUE (products_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, sku_products_id BIGINT DEFAULT NULL, status INT DEFAULT 0, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX fk_call_back_users1_idx (users_id), INDEX fk_wish_list_sku_products1_idx (sku_products_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE action_labels (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX name_UNIQUE (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A3167B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31ED2E3D59 FOREIGN KEY (sku_products_id) REFERENCES sku_products (id)');
        $this->addSql('ALTER TABLE products_base_categories ADD CONSTRAINT FK_E1A54CDFA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE products_base_categories ADD CONSTRAINT FK_E1A54CDF6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B767B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7ED2E3D59 FOREIGN KEY (sku_products_id) REFERENCES sku_products (id)');
        $this->addSql('DROP INDEX alias_UNIQUE ON call_back');
        $this->addSql('ALTER TABLE call_back DROP alias');
        $this->addSql('CREATE UNIQUE INDEX name_UNIQUE ON menu (name)');
        $this->addSql('DROP INDEX bool ON categories');
        $this->addSql('ALTER TABLE categories ADD seo_title VARCHAR(255) DEFAULT NULL, ADD seo_description VARCHAR(255) DEFAULT NULL, ADD seo_keywords VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX bool ON categories (active, in_menu)');
        $this->addSql('ALTER TABLE pages CHANGE seo_title seo_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sku_products ADD colors_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sku_products ADD CONSTRAINT FK_30E7C15F5C002039 FOREIGN KEY (colors_id) REFERENCES colors (id)');
        $this->addSql('CREATE INDEX fk_sku_products_colors1_idx ON sku_products (colors_id)');
        $this->addSql('ALTER TABLE characteristics ADD In_filter TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD action_labels_id INT DEFAULT NULL, CHANGE seo_title seo_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A1C419F1F FOREIGN KEY (action_labels_id) REFERENCES action_labels (id)');
        $this->addSql('CREATE INDEX fk_products_action_labels1_idx ON products (action_labels_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sku_products DROP FOREIGN KEY FK_30E7C15F5C002039');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A1C419F1F');
        $this->addSql('DROP TABLE wishlist');
        $this->addSql('DROP TABLE colors');
        $this->addSql('DROP TABLE products_base_categories');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE action_labels');
        $this->addSql('ALTER TABLE call_back ADD alias VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX alias_UNIQUE ON call_back (alias)');
        $this->addSql('DROP INDEX bool ON categories');
        $this->addSql('ALTER TABLE categories DROP seo_title, DROP seo_description, DROP seo_keywords');
        $this->addSql('CREATE INDEX bool ON categories (in_menu, active)');
        $this->addSql('ALTER TABLE characteristics DROP In_filter');
        $this->addSql('DROP INDEX name_UNIQUE ON menu');
        $this->addSql('ALTER TABLE pages CHANGE seo_title seo_title VARCHAR(45) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX fk_products_action_labels1_idx ON products');
        $this->addSql('ALTER TABLE products DROP action_labels_id, CHANGE seo_title seo_title VARCHAR(45) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX fk_sku_products_colors1_idx ON sku_products');
        $this->addSql('ALTER TABLE sku_products DROP colors_id');
    }
}
