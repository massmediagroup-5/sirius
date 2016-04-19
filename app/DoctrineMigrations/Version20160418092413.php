<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160418092413 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cart_product_size DROP FOREIGN KEY FK_CA794A21AD5CDBF');
        $this->addSql('CREATE TABLE product_model_image (id INT AUTO_INCREMENT NOT NULL, model_id BIGINT DEFAULT NULL, link VARCHAR(150) DEFAULT NULL COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', priority INT DEFAULT 0 NOT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX IDX_5751BAB77975B7E7 (model_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_product_size (id INT AUTO_INCREMENT NOT NULL, size_id INT DEFAULT NULL, order_id INT DEFAULT NULL, status INT DEFAULT 0, discounted_total_price NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, total_price NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, quantity INT NOT NULL, INDEX IDX_7EE892A1498DA827 (size_id), INDEX IDX_7EE892A18D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_model_image ADD CONSTRAINT FK_5751BAB77975B7E7 FOREIGN KEY (model_id) REFERENCES product_models (id)');
        $this->addSql('ALTER TABLE order_product_size ADD CONSTRAINT FK_7EE892A1498DA827 FOREIGN KEY (size_id) REFERENCES product_model_sizes (id)');
        $this->addSql('ALTER TABLE order_product_size ADD CONSTRAINT FK_7EE892A18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_product_size');
        $this->addSql('DROP TABLE product_images');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, orders_id INT DEFAULT NULL, status INT DEFAULT 0, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, discounted_total_price NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, total_price NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, quantity INT NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX fk_call_back_users1_idx (users_id), INDEX fk_cart_orders1_idx (orders_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart_product_size (id INT AUTO_INCREMENT NOT NULL, cart_id INT DEFAULT NULL, size_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_CA794A2498DA827 (size_id), INDEX IDX_CA794A21AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_images (id INT AUTO_INCREMENT NOT NULL, link VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, model_id BIGINT DEFAULT NULL, priority INT DEFAULT 0 NOT NULL, UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B767B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE cart_product_size ADD CONSTRAINT FK_CA794A21AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_product_size ADD CONSTRAINT FK_CA794A2498DA827 FOREIGN KEY (size_id) REFERENCES product_model_sizes (id)');
        $this->addSql('DROP TABLE product_model_image');
        $this->addSql('DROP TABLE order_product_size');

    }
}
