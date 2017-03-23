<?php

namespace Application\Migrations;

use AppBundle\Entity\Carriers;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170323092637 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('UPDATE `cities` SET `active` = 1 WHERE `carriers_id` = ' . Carriers::DELIVERY_ID);
        $this->addSql('UPDATE `stores` SET `active` = 1 WHERE `cities_id` IN (SELECT `id` FROM `cities` WHERE `carriers_id` = ' . Carriers::DELIVERY_ID . ')');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
