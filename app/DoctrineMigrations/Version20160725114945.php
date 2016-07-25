<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160725114945 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE history ADD user_id INT DEFAULT NULL, ADD changed VARCHAR(255) DEFAULT NULL, ADD `change_from` VARCHAR(255) DEFAULT NULL, ADD `change_to` VARCHAR(255) DEFAULT NULL, ADD additional LONGTEXT DEFAULT NULL, ADD discr VARCHAR(255) NOT NULL, CHANGE create_time create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE text change_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_27BA704BA76ED395 ON history (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BA76ED395');
        $this->addSql('DROP INDEX IDX_27BA704BA76ED395 ON history');
        $this->addSql('ALTER TABLE history ADD text VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP user_id, DROP change_type, DROP changed, DROP `change_from`, DROP `change_to`, DROP additional, DROP discr, CHANGE create_time create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
