<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170213031004 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE race_results (id INT AUTO_INCREMENT NOT NULL, race_id INT DEFAULT NULL, driver_id INT DEFAULT NULL, position INT NOT NULL, INDEX IDX_801331646E59D40D (race_id), INDEX IDX_80133164C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE race_results ADD CONSTRAINT FK_801331646E59D40D FOREIGN KEY (race_id) REFERENCES race_schedule (id)');
        $this->addSql('ALTER TABLE race_results ADD CONSTRAINT FK_80133164C3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id)');
        $this->addSql('ALTER TABLE race_submissions ADD created DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE race_results');
        $this->addSql('ALTER TABLE race_submissions DROP created');
    }
}
