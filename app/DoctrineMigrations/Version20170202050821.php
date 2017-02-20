<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170202050821 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_leagues (id INT AUTO_INCREMENT NOT NULL, league_id INT DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_5051FD9458AFC4DE (league_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_leagues ADD CONSTRAINT FK_5051FD9458AFC4DE FOREIGN KEY (league_id) REFERENCES leagues (id)');
        $this->addSql('DROP TABLE user_teams');
        $this->addSql('ALTER TABLE invite_user ADD accepted SMALLINT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_teams (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, team_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE user_leagues');
        $this->addSql('ALTER TABLE invite_user DROP accepted');
    }
}
