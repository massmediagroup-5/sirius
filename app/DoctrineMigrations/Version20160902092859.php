<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160902092859 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE email_and_sms_distribution (id INT AUTO_INCREMENT NOT NULL, sms_infos_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, send_sms TINYINT(1) DEFAULT \'0\' NOT NULL, sms_text LONGTEXT DEFAULT NULL, send_email TINYINT(1) DEFAULT \'0\' NOT NULL, email_title VARCHAR(255) DEFAULT NULL, email_text LONGTEXT DEFAULT NULL, create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX IDX_3B2EEEC032BC6DCB (sms_infos_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_and_sms_distribution_users (email_and_sms_distribution_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_7064FC03EBC580E6 (email_and_sms_distribution_id), INDEX IDX_7064FC0367B3B43D (users_id), PRIMARY KEY(email_and_sms_distribution_id, users_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sms_info (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, distribution_id INT DEFAULT NULL, sms_id VARCHAR(255) DEFAULT NULL, sms_status VARCHAR(255) DEFAULT NULL, discr VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, INDEX IDX_70A4AD018D9F6D38 (order_id), INDEX IDX_70A4AD016EB6DDB5 (distribution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_and_sms_distribution ADD CONSTRAINT FK_3B2EEEC032BC6DCB FOREIGN KEY (sms_infos_id) REFERENCES sms_info (id)');
        $this->addSql('ALTER TABLE email_and_sms_distribution_users ADD CONSTRAINT FK_7064FC03EBC580E6 FOREIGN KEY (email_and_sms_distribution_id) REFERENCES email_and_sms_distribution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE email_and_sms_distribution_users ADD CONSTRAINT FK_7064FC0367B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sms_info ADD CONSTRAINT FK_70A4AD018D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE sms_info ADD CONSTRAINT FK_70A4AD016EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES email_and_sms_distribution (id)');
        $this->addSql('ALTER TABLE call_back CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE categories CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE characteristic_values CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE filters CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE history CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE main_banners CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE main_slider CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE menu_items CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE order_product_size CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE orders ADD sms_info_id INT DEFAULT NULL, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEAD910A22 FOREIGN KEY (sms_info_id) REFERENCES sms_info (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEAD910A22 ON orders (sms_info_id)');
        $this->addSql('ALTER TABLE page_images CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_model_image CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE product_models CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, CHANGE text_label text_label VARCHAR(255) NOT NULL, CHANGE text_label_color text_label_color VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARBINARY(255) NOT NULL');
        $this->addSql('ALTER TABLE share CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE start_time start_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE end_time end_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE users DROP gray_list_flag, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, CHANGE bonuses bonuses NUMERIC(10, 2) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE vendors CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE wishlist CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE update_time update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email_and_sms_distribution_users DROP FOREIGN KEY FK_7064FC03EBC580E6');
        $this->addSql('ALTER TABLE sms_info DROP FOREIGN KEY FK_70A4AD016EB6DDB5');
        $this->addSql('ALTER TABLE email_and_sms_distribution DROP FOREIGN KEY FK_3B2EEEC032BC6DCB');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEAD910A22');
        $this->addSql('DROP TABLE email_and_sms_distribution');
        $this->addSql('DROP TABLE email_and_sms_distribution_users');
        $this->addSql('DROP TABLE sms_info');
        $this->addSql('ALTER TABLE call_back CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE categories CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristic_groups CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristic_values CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE characteristics CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE filters CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE history CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE main_banners CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE main_slider CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE menu_items CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE order_product_size CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX IDX_E52FFDEEAD910A22 ON orders');
        $this->addSql('ALTER TABLE orders DROP sms_info_id, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE page_images CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE pages CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_colors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_model_image CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_models CHANGE text_label text_label VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE text_label_color text_label_color VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARBINARY(255) NOT NULL');
        $this->addSql('ALTER TABLE share CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE start_time start_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE end_time end_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE users ADD gray_list_flag TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE bonuses bonuses NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE vendors CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE wishlist CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_time update_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
