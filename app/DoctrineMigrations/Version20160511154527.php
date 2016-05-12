<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160511154527 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE except_share_sizes_group_product_models (share_sizes_group_id INT NOT NULL, product_models_id BIGINT NOT NULL, INDEX IDX_9F9E2B4CF7FEFE28 (share_sizes_group_id), INDEX IDX_9F9E2B4CB7C609C4 (product_models_id), PRIMARY KEY(share_sizes_group_id, product_models_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE except_share_sizes_group_product_model_specific_size (share_sizes_group_id INT NOT NULL, product_model_specific_size_id INT NOT NULL, INDEX IDX_3F5AAC3DF7FEFE28 (share_sizes_group_id), INDEX IDX_3F5AAC3D6054F798 (product_model_specific_size_id), PRIMARY KEY(share_sizes_group_id, product_model_specific_size_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE except_share_sizes_group_product_models ADD CONSTRAINT FK_9F9E2B4CF7FEFE28 FOREIGN KEY (share_sizes_group_id) REFERENCES share_sizes_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE except_share_sizes_group_product_models ADD CONSTRAINT FK_9F9E2B4CB7C609C4 FOREIGN KEY (product_models_id) REFERENCES product_models (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE except_share_sizes_group_product_model_specific_size ADD CONSTRAINT FK_3F5AAC3DF7FEFE28 FOREIGN KEY (share_sizes_group_id) REFERENCES share_sizes_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE except_share_sizes_group_product_model_specific_size ADD CONSTRAINT FK_3F5AAC3D6054F798 FOREIGN KEY (product_model_specific_size_id) REFERENCES product_model_specific_size (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE except_share_sizes_group_product_models');
        $this->addSql('DROP TABLE except_share_sizes_group_product_model_specific_size');
    }
}
