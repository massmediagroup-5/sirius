<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170214100623 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_models DROP FOREIGN KEY FK_86AA71AD7AF6A82');
        $this->addSql('DROP INDEX IDX_86AA71AD7AF6A82 ON product_models');
        $this->addSql('ALTER TABLE product_models DROP share_group_id, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_model_specific_size ADD share_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_model_specific_size ADD CONSTRAINT FK_D911AA207AF6A82 FOREIGN KEY (share_group_id) REFERENCES share_sizes_group (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_D911AA207AF6A82 ON product_model_specific_size (share_group_id)');
   }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_model_image CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_model_specific_size DROP FOREIGN KEY FK_D911AA207AF6A82');
        $this->addSql('DROP INDEX IDX_D911AA207AF6A82 ON product_model_specific_size');
        $this->addSql('ALTER TABLE product_model_specific_size DROP share_group_id');
        $this->addSql('ALTER TABLE product_models ADD share_group_id INT DEFAULT NULL, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_models ADD CONSTRAINT FK_86AA71AD7AF6A82 FOREIGN KEY (share_group_id) REFERENCES share_sizes_group (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_86AA71AD7AF6A82 ON product_models (share_group_id)');
    }
}
