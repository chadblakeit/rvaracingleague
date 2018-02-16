<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180216030324 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE renew_league (id INT AUTO_INCREMENT NOT NULL, fos_user INT DEFAULT NULL, league_id INT DEFAULT NULL, season VARCHAR(255) NOT NULL, renewed SMALLINT NOT NULL, declined SMALLINT DEFAULT 0 NOT NULL, INDEX IDX_B783703A957A6479 (fos_user), INDEX IDX_B783703A58AFC4DE (league_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE renew_league ADD CONSTRAINT FK_B783703A957A6479 FOREIGN KEY (fos_user) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE renew_league ADD CONSTRAINT FK_B783703A58AFC4DE FOREIGN KEY (league_id) REFERENCES leagues (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE renew_league');
    }
}
