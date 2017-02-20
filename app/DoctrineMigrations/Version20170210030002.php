<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170210030002 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE leagues ADD fos_user INT DEFAULT NULL, DROP email');
        $this->addSql('ALTER TABLE leagues ADD CONSTRAINT FK_85CE39E957A6479 FOREIGN KEY (fos_user) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_85CE39E957A6479 ON leagues (fos_user)');
        $this->addSql('ALTER TABLE race_submissions DROP FOREIGN KEY FK_B6D19DFD8C20A0FB');
        $this->addSql('DROP INDEX IDX_B6D19DFD8C20A0FB ON race_submissions');
        $this->addSql('ALTER TABLE race_submissions CHANGE fos_user_id fos_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE race_submissions ADD CONSTRAINT FK_B6D19DFD957A6479 FOREIGN KEY (fos_user) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_B6D19DFD957A6479 ON race_submissions (fos_user)');
        $this->addSql('ALTER TABLE user_leagues ADD fos_user INT DEFAULT NULL, DROP user_id');
        $this->addSql('ALTER TABLE user_leagues ADD CONSTRAINT FK_5051FD94957A6479 FOREIGN KEY (fos_user) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_5051FD94957A6479 ON user_leagues (fos_user)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE leagues DROP FOREIGN KEY FK_85CE39E957A6479');
        $this->addSql('DROP INDEX IDX_85CE39E957A6479 ON leagues');
        $this->addSql('ALTER TABLE leagues ADD email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP fos_user');
        $this->addSql('ALTER TABLE race_submissions DROP FOREIGN KEY FK_B6D19DFD957A6479');
        $this->addSql('DROP INDEX IDX_B6D19DFD957A6479 ON race_submissions');
        $this->addSql('ALTER TABLE race_submissions CHANGE fos_user fos_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE race_submissions ADD CONSTRAINT FK_B6D19DFD8C20A0FB FOREIGN KEY (fos_user_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_B6D19DFD8C20A0FB ON race_submissions (fos_user_id)');
        $this->addSql('ALTER TABLE user_leagues DROP FOREIGN KEY FK_5051FD94957A6479');
        $this->addSql('DROP INDEX IDX_5051FD94957A6479 ON user_leagues');
        $this->addSql('ALTER TABLE user_leagues ADD user_id INT NOT NULL, DROP fos_user');
    }
}
