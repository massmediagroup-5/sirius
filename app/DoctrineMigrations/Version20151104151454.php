<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151104151454 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_variants_values DROP FOREIGN KEY FK_3063F4A7D0CF4EA');
        $this->addSql('ALTER TABLE product_models DROP FOREIGN KEY FK_86AA71AD1E1D9ABE');
        $this->addSql('CREATE TABLE product_colors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, hex VARCHAR(25) DEFAULT NULL, priority INT DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX index3 (priority), INDEX text (name), UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE product_variants');
        $this->addSql('DROP TABLE product_variants_values');
        $this->addSql('ALTER TABLE categories CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX fk_product_models_product_variants_values1_idx ON product_models');
        $this->addSql('ALTER TABLE product_models ADD description MEDIUMTEXT DEFAULT NULL, ADD priority INT DEFAULT NULL, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, CHANGE product_variants_values_id product_colors_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_models ADD CONSTRAINT FK_86AA71AD32A096B4 FOREIGN KEY (product_colors_id) REFERENCES product_colors (id)');
        $this->addSql('CREATE INDEX fk_product_models_product_colors1_idx ON product_models (product_colors_id)');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_models DROP FOREIGN KEY FK_86AA71AD32A096B4');
        $this->addSql('CREATE TABLE product_variants (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, description MEDIUMTEXT DEFAULT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX id_UNIQUE (id), INDEX text (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_variants_values (id INT AUTO_INCREMENT NOT NULL, product_variants_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, value VARCHAR(25) DEFAULT NULL COLLATE utf8_unicode_ci, priority INT DEFAULT NULL, create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX index3 (priority), INDEX text (name), INDEX fk_product_variants_values_product_variants1_idx (product_variants_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_variants_values ADD CONSTRAINT FK_3063F4A7D0CF4EA FOREIGN KEY (product_variants_id) REFERENCES product_variants (id)');
        $this->addSql('DROP TABLE product_colors');
        $this->addSql('ALTER TABLE categories CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX fk_product_models_product_colors1_idx ON product_models');
        $this->addSql('ALTER TABLE product_models ADD product_variants_values_id INT DEFAULT NULL, DROP product_colors_id, DROP description, DROP priority, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_models ADD CONSTRAINT FK_86AA71AD1E1D9ABE FOREIGN KEY (product_variants_values_id) REFERENCES product_variants_values (id)');
        $this->addSql('CREATE INDEX fk_product_models_product_variants_values1_idx ON product_models (product_variants_values_id)');
    }
}
