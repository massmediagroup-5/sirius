<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151201182856 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $sql = file_get_contents( __DIR__ . '/20151201182856.sql');
        $this->addSql($sql);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE `action_labels`');
        $this->addSql('TRUNCATE `menu`');
        $this->addSql('TRUNCATE `menu_items`');
        $this->addSql('TRUNCATE `pages`');
        $this->addSql('TRUNCATE `site_params`');
        $this->addSql('TRUNCATE `users`');
        $this->addSql('TRUNCATE `vendors`');
        $this->addSql('TRUNCATE `product_colors`');
    }
}
