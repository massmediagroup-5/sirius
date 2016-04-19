<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160415111418 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_model_specific_size (id INT AUTO_INCREMENT NOT NULL, model_id BIGINT DEFAULT NULL, size_id INT DEFAULT NULL, price NUMERIC(10, 0) NOT NULL, wholesale_price NUMERIC(10, 0) NOT NULL, pre_order_flag TINYINT(1) NOT NULL, quantity INT NOT NULL, INDEX IDX_D911AA207975B7E7 (model_id), INDEX IDX_D911AA20498DA827 (size_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_model_specific_size ADD CONSTRAINT FK_D911AA207975B7E7 FOREIGN KEY (model_id) REFERENCES product_models (id)');
        $this->addSql('ALTER TABLE product_model_specific_size ADD CONSTRAINT FK_D911AA20498DA827 FOREIGN KEY (size_id) REFERENCES product_model_sizes (id)');
        $this->addSql('DROP TABLE product_models_has_sizes');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARBINARY(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_models_has_sizes (product_model_id BIGINT NOT NULL, size_id INT NOT NULL, INDEX IDX_6BBE44B1B2C5DD70 (product_model_id), INDEX IDX_6BBE44B1498DA827 (size_id), PRIMARY KEY(product_model_id, size_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_models_has_sizes ADD CONSTRAINT FK_6BBE44B1498DA827 FOREIGN KEY (size_id) REFERENCES product_model_sizes (id)');
        $this->addSql('ALTER TABLE product_models_has_sizes ADD CONSTRAINT FK_6BBE44B1B2C5DD70 FOREIGN KEY (product_model_id) REFERENCES product_models (id)');
        $this->addSql('DROP TABLE product_model_specific_size');
    }
}
