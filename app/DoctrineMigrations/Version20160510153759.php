<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160510153759 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE share_sizes_group_products (share_sizes_group_id INT NOT NULL, products_id INT NOT NULL, INDEX IDX_1DCE9A50F7FEFE28 (share_sizes_group_id), INDEX IDX_1DCE9A506C8A81A9 (products_id), PRIMARY KEY(share_sizes_group_id, products_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share_sizes_group_product_models (share_sizes_group_id INT NOT NULL, product_models_id BIGINT NOT NULL, INDEX IDX_F99FFDB1F7FEFE28 (share_sizes_group_id), INDEX IDX_F99FFDB1B7C609C4 (product_models_id), PRIMARY KEY(share_sizes_group_id, product_models_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share_sizes_group_product_model_specific_size (share_sizes_group_id INT NOT NULL, product_model_specific_size_id INT NOT NULL, INDEX IDX_CC1EAE2BF7FEFE28 (share_sizes_group_id), INDEX IDX_CC1EAE2B6054F798 (product_model_specific_size_id), PRIMARY KEY(share_sizes_group_id, product_model_specific_size_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share_sizes_group_product_model_sizes (share_sizes_group_id INT NOT NULL, product_model_sizes_id INT NOT NULL, INDEX IDX_AC6A08C3F7FEFE28 (share_sizes_group_id), INDEX IDX_AC6A08C3DB81AA29 (product_model_sizes_id), PRIMARY KEY(share_sizes_group_id, product_model_sizes_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share_sizes_group_characteristic_values (share_sizes_group_id INT NOT NULL, characteristic_values_id INT NOT NULL, INDEX IDX_6F56D82DF7FEFE28 (share_sizes_group_id), INDEX IDX_6F56D82DF7C3C4DC (characteristic_values_id), PRIMARY KEY(share_sizes_group_id, characteristic_values_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share_sizes_group_product_colors (share_sizes_group_id INT NOT NULL, product_colors_id INT NOT NULL, INDEX IDX_DFF70E27F7FEFE28 (share_sizes_group_id), INDEX IDX_DFF70E2732A096B4 (product_colors_id), PRIMARY KEY(share_sizes_group_id, product_colors_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE share_sizes_group_products ADD CONSTRAINT FK_1DCE9A50F7FEFE28 FOREIGN KEY (share_sizes_group_id) REFERENCES share_sizes_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_products ADD CONSTRAINT FK_1DCE9A506C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_product_models ADD CONSTRAINT FK_F99FFDB1F7FEFE28 FOREIGN KEY (share_sizes_group_id) REFERENCES share_sizes_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_product_models ADD CONSTRAINT FK_F99FFDB1B7C609C4 FOREIGN KEY (product_models_id) REFERENCES product_models (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_product_model_specific_size ADD CONSTRAINT FK_CC1EAE2BF7FEFE28 FOREIGN KEY (share_sizes_group_id) REFERENCES share_sizes_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_product_model_specific_size ADD CONSTRAINT FK_CC1EAE2B6054F798 FOREIGN KEY (product_model_specific_size_id) REFERENCES product_model_specific_size (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_product_model_sizes ADD CONSTRAINT FK_AC6A08C3F7FEFE28 FOREIGN KEY (share_sizes_group_id) REFERENCES share_sizes_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_product_model_sizes ADD CONSTRAINT FK_AC6A08C3DB81AA29 FOREIGN KEY (product_model_sizes_id) REFERENCES product_model_sizes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_characteristic_values ADD CONSTRAINT FK_6F56D82DF7FEFE28 FOREIGN KEY (share_sizes_group_id) REFERENCES share_sizes_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_characteristic_values ADD CONSTRAINT FK_6F56D82DF7C3C4DC FOREIGN KEY (characteristic_values_id) REFERENCES characteristic_values (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_product_colors ADD CONSTRAINT FK_DFF70E27F7FEFE28 FOREIGN KEY (share_sizes_group_id) REFERENCES share_sizes_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_sizes_group_product_colors ADD CONSTRAINT FK_DFF70E2732A096B4 FOREIGN KEY (product_colors_id) REFERENCES product_colors (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE share_characteristic_values');
        $this->addSql('DROP TABLE share_product_colors');
        $this->addSql('DROP TABLE share_product_model_sizes');
        $this->addSql('DROP TABLE share_product_model_specific_size');
        $this->addSql('DROP TABLE share_product_models');
        $this->addSql('DROP TABLE share_products');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE history CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_models CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_model_image CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_values CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARBINARY(255) NOT NULL');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE share CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE page_images CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE main_banners CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE filters CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE menu_items CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE order_product_size CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE orders CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE categories CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
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

        $this->addSql('CREATE TABLE share_characteristic_values (share_id INT NOT NULL, characteristic_values_id INT NOT NULL, INDEX IDX_B9C4B6C82AE63FDB (share_id), INDEX IDX_B9C4B6C8F7C3C4DC (characteristic_values_id), PRIMARY KEY(share_id, characteristic_values_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share_product_colors (share_id INT NOT NULL, product_colors_id INT NOT NULL, INDEX IDX_5DF2A6012AE63FDB (share_id), INDEX IDX_5DF2A60132A096B4 (product_colors_id), PRIMARY KEY(share_id, product_colors_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share_product_model_sizes (share_id INT NOT NULL, product_model_sizes_id INT NOT NULL, INDEX IDX_654ADBAF2AE63FDB (share_id), INDEX IDX_654ADBAFDB81AA29 (product_model_sizes_id), PRIMARY KEY(share_id, product_model_sizes_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share_product_model_specific_size (share_id INT NOT NULL, product_model_specific_size_id INT NOT NULL, INDEX IDX_32D88D9B2AE63FDB (share_id), INDEX IDX_32D88D9B6054F798 (product_model_specific_size_id), PRIMARY KEY(share_id, product_model_specific_size_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share_product_models (share_id INT NOT NULL, product_models_id BIGINT NOT NULL, INDEX IDX_7B9A55972AE63FDB (share_id), INDEX IDX_7B9A5597B7C609C4 (product_models_id), PRIMARY KEY(share_id, product_models_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share_products (share_id INT NOT NULL, products_id INT NOT NULL, INDEX IDX_4A41E99F2AE63FDB (share_id), INDEX IDX_4A41E99F6C8A81A9 (products_id), PRIMARY KEY(share_id, products_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE share_characteristic_values ADD CONSTRAINT FK_B9C4B6C82AE63FDB FOREIGN KEY (share_id) REFERENCES share (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_characteristic_values ADD CONSTRAINT FK_B9C4B6C8F7C3C4DC FOREIGN KEY (characteristic_values_id) REFERENCES characteristic_values (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_product_colors ADD CONSTRAINT FK_5DF2A6012AE63FDB FOREIGN KEY (share_id) REFERENCES share (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_product_colors ADD CONSTRAINT FK_5DF2A60132A096B4 FOREIGN KEY (product_colors_id) REFERENCES product_colors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_product_model_sizes ADD CONSTRAINT FK_654ADBAF2AE63FDB FOREIGN KEY (share_id) REFERENCES share (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_product_model_sizes ADD CONSTRAINT FK_654ADBAFDB81AA29 FOREIGN KEY (product_model_sizes_id) REFERENCES product_model_sizes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_product_model_specific_size ADD CONSTRAINT FK_32D88D9B2AE63FDB FOREIGN KEY (share_id) REFERENCES share (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_product_model_specific_size ADD CONSTRAINT FK_32D88D9B6054F798 FOREIGN KEY (product_model_specific_size_id) REFERENCES product_model_specific_size (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_product_models ADD CONSTRAINT FK_7B9A55972AE63FDB FOREIGN KEY (share_id) REFERENCES share (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_product_models ADD CONSTRAINT FK_7B9A5597B7C609C4 FOREIGN KEY (product_models_id) REFERENCES product_models (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_products ADD CONSTRAINT FK_4A41E99F2AE63FDB FOREIGN KEY (share_id) REFERENCES share (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE share_products ADD CONSTRAINT FK_4A41E99F6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE share_sizes_group_products');
        $this->addSql('DROP TABLE share_sizes_group_product_models');
        $this->addSql('DROP TABLE share_sizes_group_product_model_specific_size');
        $this->addSql('DROP TABLE share_sizes_group_product_model_sizes');
        $this->addSql('DROP TABLE share_sizes_group_characteristic_values');
        $this->addSql('DROP TABLE share_sizes_group_product_colors');
        $this->addSql('ALTER TABLE call_back CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE categories CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristic_values CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE filters CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE history CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE main_banners CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE main_slider CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE menu_items CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE order_product_size CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE orders CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE page_images CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_model_image CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_models CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARBINARY(255) NOT NULL');
        $this->addSql('ALTER TABLE share CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE vendors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE wishlist CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
