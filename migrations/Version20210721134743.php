<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210721134743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_license DROP FOREIGN KEY FK_F65EA13460F904B');
        $this->addSql('ALTER TABLE user_license DROP FOREIGN KEY FK_F65EA13A76ED395');
        $this->addSql('ALTER TABLE user_license ADD id INT AUTO_INCREMENT NOT NULL, ADD picture VARCHAR(255) DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user_license ADD CONSTRAINT FK_F65EA13460F904B FOREIGN KEY (license_id) REFERENCES license (id)');
        $this->addSql('ALTER TABLE user_license ADD CONSTRAINT FK_F65EA13A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_license MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE user_license DROP FOREIGN KEY FK_F65EA13A76ED395');
        $this->addSql('ALTER TABLE user_license DROP FOREIGN KEY FK_F65EA13460F904B');
        $this->addSql('ALTER TABLE user_license DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_license DROP id, DROP picture');
        $this->addSql('ALTER TABLE user_license ADD CONSTRAINT FK_F65EA13A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_license ADD CONSTRAINT FK_F65EA13460F904B FOREIGN KEY (license_id) REFERENCES license (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_license ADD PRIMARY KEY (user_id, license_id)');
    }
}
