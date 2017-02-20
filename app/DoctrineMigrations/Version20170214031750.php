<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170214031750 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE race_results DROP FOREIGN KEY FK_80133164C3423909');
        $this->addSql('DROP INDEX IDX_80133164C3423909 ON race_results');
        $this->addSql('ALTER TABLE race_results ADD results LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', DROP driver_id, DROP position');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE race_results ADD driver_id INT DEFAULT NULL, ADD position INT NOT NULL, DROP results');
        $this->addSql('ALTER TABLE race_results ADD CONSTRAINT FK_80133164C3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id)');
        $this->addSql('CREATE INDEX IDX_80133164C3423909 ON race_results (driver_id)');
    }
}
