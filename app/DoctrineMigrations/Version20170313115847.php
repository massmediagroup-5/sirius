<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170313115847 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE `orders` SET `status_id` = (SELECT `id` FROM `order_status` WHERE `code` = 'sent') WHERE `status_id` IN (SELECT `id` FROM `order_status` WHERE `name` LIKE '%Доставлено в пункт назначения%')");

        $this->addSql("DELETE FROM `order_status` WHERE `name` LIKE '%Доставлено в пункт назначения%'");
        $this->addSql("INSERT INTO `order_status` (`name`,`code`,`description`,`base_flag`,`priority`,`send_client`,`send_manager`,`active`,`send_client_text`,`send_manager_text`,`send_client_email`,`send_client_email_text`,`send_client_night_text`) VALUES ('Доставлено в пункт назначения','delivered',NULL,1,7,0,0,1,'Ваш заказ %s  был доставлен в пункт назначения','Заказ %s  был доставлен в пункт назначения',0,'Ваш заказ %s  был доставлен в пункт назначения',NULL)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE `orders` SET `status_id` = (SELECT `id` FROM `order_status` WHERE `code` = 'sent') WHERE `status_id` = (SELECT `id` FROM `order_status` WHERE `code` = 'delivered')");

        $this->addSql("DELETE FROM `order_status` WHERE `code` = 'delivered'");
        $this->addSql("INSERT INTO `order_status` (`name`,`code`,`description`,`base_flag`,`priority`,`send_client`,`send_manager`,`active`,`send_client_text`,`send_manager_text`,`send_client_email`,`send_client_email_text`,`send_client_night_text`) VALUES ('Доставлено в пункт назначения','',NULL,0,7,1,1,1,'Ваш заказ %s  был доставлен в пункт назначения','Заказ %s  был доставлен в пункт назначения',1,'Ваш заказ %s  был доставлен в пункт назначения',NULL)");
    }
}
