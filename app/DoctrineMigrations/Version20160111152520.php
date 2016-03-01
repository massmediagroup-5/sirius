<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160111152520 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(' SET FOREIGN_KEY_CHECKS = 0;
                        TRUNCATE `categories`;
                        TRUNCATE `categories_has_characteristic_values`;
                        TRUNCATE `characteristics`;
                        TRUNCATE `characteristic_groups`;
                        TRUNCATE `characteristic_priorities`;
                        TRUNCATE `characteristic_values`;
                        TRUNCATE `products`;
                        TRUNCATE `products_base_categories`;
                        TRUNCATE `products_has_categories`;
                        TRUNCATE `products_has_characteristic_values`;
                        TRUNCATE `product_colors`;
                        TRUNCATE `product_images`;
                        TRUNCATE `product_models`;
                        TRUNCATE `product_model_images`;
                        TRUNCATE `sku_products`;
                        SET FOREIGN_KEY_CHECKS = 1;
        ');

        $this->addSql('INSERT INTO `categories` (`id`, `name`, `alias`, `parrent_id`, `in_menu`, `active`, `seo_title`, `seo_description`, `seo_keywords`) VALUES (1, "Базовая", "all", NULL, 1, 1, NULL, NULL, NULL);');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(' SET FOREIGN_KEY_CHECKS = 0;
                        TRUNCATE `categories`;
                        TRUNCATE `categories_has_characteristic_values`;
                        TRUNCATE `characteristics`;
                        TRUNCATE `characteristic_groups`;
                        TRUNCATE `characteristic_priorities`;
                        TRUNCATE `characteristic_values`;
                        TRUNCATE `products`;
                        TRUNCATE `products_base_categories`;
                        TRUNCATE `products_has_categories`;
                        TRUNCATE `products_has_characteristic_values`;
                        TRUNCATE `product_colors`;
                        TRUNCATE `product_images`;
                        TRUNCATE `product_models`;
                        TRUNCATE `product_model_images`;
                        TRUNCATE `sku_products`;
                        SET FOREIGN_KEY_CHECKS = 1;
        ');

    }
}
