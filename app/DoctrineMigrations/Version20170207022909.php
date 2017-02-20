<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170207022909 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE drivers CHANGE firstname firstname VARCHAR(30) NOT NULL, CHANGE lastname lastname VARCHAR(40) NOT NULL, CHANGE sponsor sponsor VARCHAR(100) NOT NULL, CHANGE team team VARCHAR(40) NOT NULL, CHANGE carmake carmake VARCHAR(20) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE drivers CHANGE firstname firstname VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE lastname lastname VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE sponsor sponsor VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE team team VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE carmake carmake VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
