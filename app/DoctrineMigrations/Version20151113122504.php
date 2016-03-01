<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151113122504 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_roles_has_users DROP FOREIGN KEY FK_B3F80FF9D84AB5C4');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE user_roles_has_users');
        $this->addSql('ALTER TABLE product_model_images CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE products_base_categories CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE categories CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_priorities CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_models CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_images CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE sku_products CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX id_UNIQUE (id), UNIQUE INDEX name_UNIQUE (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles_has_users (users_id INT NOT NULL, user_roles_id INT NOT NULL, INDEX IDX_B3F80FF967B3B43D (users_id), INDEX IDX_B3F80FF9D84AB5C4 (user_roles_id), PRIMARY KEY(users_id, user_roles_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_roles_has_users ADD CONSTRAINT FK_B3F80FF967B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_roles_has_users ADD CONSTRAINT FK_B3F80FF9D84AB5C4 FOREIGN KEY (user_roles_id) REFERENCES user_roles (id)');
        $this->addSql('ALTER TABLE categories CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristic_priorities CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_images CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_model_images CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_models CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE products_base_categories CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE sku_products CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
