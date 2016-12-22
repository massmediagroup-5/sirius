<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161221142651 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE loyalty_program ADD discr VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE loyalty_program SET discr="simple"');

        $this->addSql('INSERT INTO `loyalty_program` (`sum_from`,`sum_to`,`discount`,`discr`) VALUES (10000,15000,1,\'wholesaler\')');
        $this->addSql('INSERT INTO `loyalty_program` (`sum_from`,`sum_to`,`discount`,`discr`) VALUES (15001,20000,3,\'wholesaler\')');
        $this->addSql('INSERT INTO `loyalty_program` (`sum_from`,`sum_to`,`discount`,`discr`) VALUES (20001,30000,5,\'wholesaler\')');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM loyalty_program WHERE discr = \'wholesaler\'');
        $this->addSql('ALTER TABLE loyalty_program DROP discr');
    }
}
