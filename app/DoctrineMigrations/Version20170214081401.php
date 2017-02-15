<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170214081401 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_model_specific_size_share_sizes_group (product_model_specific_size_id INT NOT NULL, share_sizes_group_id INT NOT NULL, INDEX IDX_ECA988B36054F798 (product_model_specific_size_id), INDEX IDX_ECA988B3F7FEFE28 (share_sizes_group_id), PRIMARY KEY(product_model_specific_size_id, share_sizes_group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_model_specific_size_share_sizes_group ADD CONSTRAINT FK_ECA988B36054F798 FOREIGN KEY (product_model_specific_size_id) REFERENCES product_model_specific_size (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_model_specific_size_share_sizes_group ADD CONSTRAINT FK_ECA988B3F7FEFE28 FOREIGN KEY (share_sizes_group_id) REFERENCES share_sizes_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_model_specific_size DROP FOREIGN KEY FK_D911AA207AF6A82');
        $this->addSql('DROP INDEX IDX_D911AA207AF6A82 ON product_model_specific_size');
        $this->addSql('ALTER TABLE product_model_specific_size DROP share_group_id');
        $this->addSql('ALTER TABLE share CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE start_time start_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE end_time end_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_model_specific_size_share_sizes_group');
        $this->addSql('ALTER TABLE product_model_specific_size ADD share_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_model_specific_size ADD CONSTRAINT FK_D911AA207AF6A82 FOREIGN KEY (share_group_id) REFERENCES share_sizes_group (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_D911AA207AF6A82 ON product_model_specific_size (share_group_id)');
        $this->addSql('ALTER TABLE share CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE start_time start_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE end_time end_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE add_bonuses_at add_bonuses_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
