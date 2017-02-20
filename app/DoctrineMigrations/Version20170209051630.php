<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170209051630 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE race_submissions DROP INDEX UNIQ_B6D19DFD8C20A0FB, ADD INDEX IDX_B6D19DFD8C20A0FB (fos_user_id)');
        $this->addSql('ALTER TABLE race_submissions DROP INDEX UNIQ_B6D19DFD58AFC4DE, ADD INDEX IDX_B6D19DFD58AFC4DE (league_id)');
        $this->addSql('ALTER TABLE race_submissions DROP INDEX UNIQ_B6D19DFD6E59D40D, ADD INDEX IDX_B6D19DFD6E59D40D (race_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE race_submissions DROP INDEX IDX_B6D19DFD8C20A0FB, ADD UNIQUE INDEX UNIQ_B6D19DFD8C20A0FB (fos_user_id)');
        $this->addSql('ALTER TABLE race_submissions DROP INDEX IDX_B6D19DFD58AFC4DE, ADD UNIQUE INDEX UNIQ_B6D19DFD58AFC4DE (league_id)');
        $this->addSql('ALTER TABLE race_submissions DROP INDEX IDX_B6D19DFD6E59D40D, ADD UNIQUE INDEX UNIQ_B6D19DFD6E59D40D (race_id)');
    }
}
