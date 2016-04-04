<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160404124345 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO `site_params` (`param_name`, `param_value`) VALUES
                    ('fb_link', 'https://ru-ru.facebook.com/people/Sportwear-Sirius/100010135187519'),
                    ('vk_link', 'http://vk.com/sirius_sport'),
                    ('gp_link', 'https://plus.google.com/u/0/112198292170737630961/about')"
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM `site_params` WHERE `param_name` IN ('fb_link', 'vk_link', 'gp_link')");
    }
}
