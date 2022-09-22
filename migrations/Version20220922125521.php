<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220922125521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE live DROP FOREIGN KEY FK_530F2CAF84A0A3ED');
        $this->addSql('ALTER TABLE live DROP description, DROP title, CHANGE content_id content_id INT NOT NULL');
        $this->addSql('ALTER TABLE live ADD CONSTRAINT FK_530F2CAF84A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
        $this->addSql('ALTER TABLE video CHANGE status status varchar(8) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE live DROP FOREIGN KEY FK_530F2CAF84A0A3ED');
        $this->addSql('ALTER TABLE live ADD description LONGTEXT NOT NULL, ADD title VARCHAR(255) NOT NULL, CHANGE content_id content_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE live ADD CONSTRAINT FK_530F2CAF84A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE video CHANGE status status VARCHAR(8) NOT NULL');
    }
}
