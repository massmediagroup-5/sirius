<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160414143827 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_model_decoration_colors');
        $this->addSql('DROP TABLE product_model_images');
        $this->addSql('ALTER TABLE product_images ADD priority INT DEFAULT 0 NOT NULL');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_model_decoration_colors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, hex VARCHAR(25) DEFAULT NULL COLLATE utf8_unicode_ci, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX name (name), INDEX hex (hex), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_model_images (id INT AUTO_INCREMENT NOT NULL, product_models_id BIGINT DEFAULT NULL, link VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d.png\', create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, priority INT DEFAULT 0 NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX fk_product_images_product_models1_idx (product_models_id), INDEX link (link), INDEX priority (priority), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_model_images ADD CONSTRAINT FK_4E5CB89EB7C609C4 FOREIGN KEY (product_models_id) REFERENCES product_models (id)');
        $this->addSql('ALTER TABLE product_images DROP priority');

    }
}
