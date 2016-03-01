<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151023152552 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE products_has_filter_sets DROP FOREIGN KEY FK_DDBB7B14D9EF4B14');
        $this->addSql('ALTER TABLE characteristics DROP FOREIGN KEY FK_7037B1566B715464');
        $this->addSql('ALTER TABLE filter_sets DROP FOREIGN KEY FK_FCB722A86B715464');
        $this->addSql('CREATE TABLE categories_has_characteristic_values (categories_id INT NOT NULL, characteristic_values_id INT NOT NULL, INDEX IDX_D25FA0CFA21214B7 (categories_id), INDEX IDX_D25FA0CFF7C3C4DC (characteristic_values_id), PRIMARY KEY(categories_id, characteristic_values_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristic_groups (id INT AUTO_INCREMENT NOT NULL, filters_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX text (name), UNIQUE INDEX ID_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristic_values (id INT AUTO_INCREMENT NOT NULL, characteristics_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX text (name), INDEX fk_characteristic_values_characteristics1_idx (characteristics_id), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_has_categories (products_id INT NOT NULL, categories_id INT NOT NULL, INDEX IDX_825E4F5C6C8A81A9 (products_id), INDEX IDX_825E4F5CA21214B7 (categories_id), PRIMARY KEY(products_id, categories_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_has_characteristic_values (products_id INT NOT NULL, characteristic_values_id INT NOT NULL, INDEX IDX_8899C6F56C8A81A9 (products_id), INDEX IDX_8899C6F5F7C3C4DC (characteristic_values_id), PRIMARY KEY(products_id, characteristic_values_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categories_has_characteristic_values ADD CONSTRAINT FK_D25FA0CFA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE categories_has_characteristic_values ADD CONSTRAINT FK_D25FA0CFF7C3C4DC FOREIGN KEY (characteristic_values_id) REFERENCES characteristic_values (id)');
        $this->addSql('ALTER TABLE characteristic_values ADD CONSTRAINT FK_FCC77D084B13ADB4 FOREIGN KEY (characteristics_id) REFERENCES characteristics (id)');
        $this->addSql('ALTER TABLE products_has_categories ADD CONSTRAINT FK_825E4F5C6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_has_categories ADD CONSTRAINT FK_825E4F5CA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE products_has_characteristic_values ADD CONSTRAINT FK_8899C6F56C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_has_characteristic_values ADD CONSTRAINT FK_8899C6F5F7C3C4DC FOREIGN KEY (characteristic_values_id) REFERENCES characteristic_values (id)');
        $this->addSql('DROP TABLE filter_sets');
        $this->addSql('DROP TABLE filters');
        $this->addSql('DROP TABLE products_has_filter_sets');
        $this->addSql('ALTER TABLE menu_items ADD active TINYINT(1) DEFAULT NULL, CHANGE priority priority INT DEFAULT NULL');
        $this->addSql('DROP INDEX fk_characteristics_filters1_idx ON characteristics');
        $this->addSql('ALTER TABLE characteristics CHANGE filters_id characteristic_groups_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE characteristics ADD CONSTRAINT FK_7037B15627A4A649 FOREIGN KEY (characteristic_groups_id) REFERENCES characteristic_groups (id)');
        $this->addSql('CREATE INDEX fk_characteristics_characteristic_groups1_idx ON characteristics (characteristic_groups_id)');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A12469DE2');
        $this->addSql('DROP INDEX fk_product_category_idx ON products');
        $this->addSql('ALTER TABLE products DROP category_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE characteristics DROP FOREIGN KEY FK_7037B15627A4A649');
        $this->addSql('ALTER TABLE categories_has_characteristic_values DROP FOREIGN KEY FK_D25FA0CFF7C3C4DC');
        $this->addSql('ALTER TABLE products_has_characteristic_values DROP FOREIGN KEY FK_8899C6F5F7C3C4DC');
        $this->addSql('CREATE TABLE filter_sets (id INT AUTO_INCREMENT NOT NULL, filters_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX ID_UNIQUE (id), INDEX fk_filters_sets_filters1_idx (filters_id), INDEX text (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filters (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX text (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_has_filter_sets (products_id INT NOT NULL, filters_sets_id INT NOT NULL, INDEX IDX_DDBB7B146C8A81A9 (products_id), INDEX IDX_DDBB7B14D9EF4B14 (filters_sets_id), PRIMARY KEY(products_id, filters_sets_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE filter_sets ADD CONSTRAINT FK_FCB722A86B715464 FOREIGN KEY (filters_id) REFERENCES filters (id)');
        $this->addSql('ALTER TABLE products_has_filter_sets ADD CONSTRAINT FK_DDBB7B146C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_has_filter_sets ADD CONSTRAINT FK_DDBB7B14D9EF4B14 FOREIGN KEY (filters_sets_id) REFERENCES filter_sets (id)');
        $this->addSql('DROP TABLE categories_has_characteristic_values');
        $this->addSql('DROP TABLE characteristic_groups');
        $this->addSql('DROP TABLE characteristic_values');
        $this->addSql('DROP TABLE products_has_categories');
        $this->addSql('DROP TABLE products_has_characteristic_values');
        $this->addSql('DROP INDEX fk_characteristics_characteristic_groups1_idx ON characteristics');
        $this->addSql('ALTER TABLE characteristics CHANGE characteristic_groups_id filters_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE characteristics ADD CONSTRAINT FK_7037B1566B715464 FOREIGN KEY (filters_id) REFERENCES filters (id)');
        $this->addSql('CREATE INDEX fk_characteristics_filters1_idx ON characteristics (filters_id)');
        $this->addSql('ALTER TABLE menu_items DROP active, CHANGE priority priority TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX fk_product_category_idx ON products (category_id)');
    }
}
