<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170403093142 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE distribution_email_info (id INT AUTO_INCREMENT NOT NULL, distribution_id INT DEFAULT NULL, user_id INT DEFAULT NULL, sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX IDX_40F9DADD6EB6DDB5 (distribution_id), INDEX IDX_40F9DADDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE distribution_email_info ADD CONSTRAINT FK_40F9DADD6EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES email_and_sms_distribution (id)');
        $this->addSql('ALTER TABLE distribution_email_info ADD CONSTRAINT FK_40F9DADDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE distribution_email_info');
    }
}
