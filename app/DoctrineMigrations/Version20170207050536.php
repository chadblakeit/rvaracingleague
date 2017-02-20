<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170207050536 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE race_submissions (id INT AUTO_INCREMENT NOT NULL, fos_user_id INT DEFAULT NULL, league_id INT DEFAULT NULL, race_id INT DEFAULT NULL, drivers LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX UNIQ_B6D19DFD8C20A0FB (fos_user_id), UNIQUE INDEX UNIQ_B6D19DFD58AFC4DE (league_id), UNIQUE INDEX UNIQ_B6D19DFD6E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE race_submissions ADD CONSTRAINT FK_B6D19DFD8C20A0FB FOREIGN KEY (fos_user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE race_submissions ADD CONSTRAINT FK_B6D19DFD58AFC4DE FOREIGN KEY (league_id) REFERENCES leagues (id)');
        $this->addSql('ALTER TABLE race_submissions ADD CONSTRAINT FK_B6D19DFD6E59D40D FOREIGN KEY (race_id) REFERENCES race_schedule (id)');
        $this->addSql('DROP TABLE team_submissions');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE team_submissions (id INT AUTO_INCREMENT NOT NULL, race_id INT DEFAULT NULL, league_id INT DEFAULT NULL, fos_user_id INT DEFAULT NULL, drivers LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX UNIQ_D45186BA8C20A0FB (fos_user_id), UNIQUE INDEX UNIQ_D45186BA58AFC4DE (league_id), UNIQUE INDEX UNIQ_D45186BA6E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE team_submissions ADD CONSTRAINT FK_D45186BA6E59D40D FOREIGN KEY (race_id) REFERENCES race_schedule (id)');
        $this->addSql('ALTER TABLE team_submissions ADD CONSTRAINT FK_D45186BA58AFC4DE FOREIGN KEY (league_id) REFERENCES leagues (id)');
        $this->addSql('ALTER TABLE team_submissions ADD CONSTRAINT FK_D45186BA8C20A0FB FOREIGN KEY (fos_user_id) REFERENCES fos_user (id)');
        $this->addSql('DROP TABLE race_submissions');
    }
}
