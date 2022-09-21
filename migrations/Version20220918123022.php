<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220918123022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge ADD repository VARCHAR(255) DEFAULT NULL, CHANGE live_id live_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE video CHANGE status status varchar(8) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge DROP repository, CHANGE live_id live_id INT NOT NULL');
        $this->addSql('ALTER TABLE video CHANGE status status VARCHAR(8) NOT NULL');
    }
}
