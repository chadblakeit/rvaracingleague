<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170130043055 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invite_user CHANGE league_id league_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invite_user ADD CONSTRAINT FK_95A717C358AFC4DE FOREIGN KEY (league_id) REFERENCES leagues (id)');
        $this->addSql('CREATE INDEX IDX_95A717C358AFC4DE ON invite_user (league_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invite_user DROP FOREIGN KEY FK_95A717C358AFC4DE');
        $this->addSql('DROP INDEX IDX_95A717C358AFC4DE ON invite_user');
        $this->addSql('ALTER TABLE invite_user CHANGE league_id league_id INT NOT NULL');
    }
}
