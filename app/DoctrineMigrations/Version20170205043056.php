<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170205043056 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE drivers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, number INT NOT NULL, sponsor VARCHAR(255) NOT NULL, team VARCHAR(255) NOT NULL, carmake VARCHAR(255) NOT NULL, inactive SMALLINT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE race_schedule (id INT AUTO_INCREMENT NOT NULL, racename VARCHAR(255) NOT NULL, racedate DATETIME NOT NULL, qualifyingdate DATETIME NOT NULL, location VARCHAR(255) NOT NULL, trackname VARCHAR(255) NOT NULL, tracklength NUMERIC(10, 0) NOT NULL, logo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE drivers');
        $this->addSql('DROP TABLE race_schedule');
    }
}
