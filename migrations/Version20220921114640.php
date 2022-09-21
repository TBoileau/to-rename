<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220921114640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge_rule CHANGE challenge_id challenge_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE live ADD duration_hours INT NOT NULL, ADD duration_minutes INT NOT NULL, ADD duration_seconds INT NOT NULL');
        $this->addSql('ALTER TABLE video CHANGE status status varchar(8) NOT NULL');
        $this->addSql('UPDATE live set duration_hours = 2, duration_minutes = 0, duration_seconds = 0 WHERE 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE live DROP duration_hours, DROP duration_minutes, DROP duration_seconds');
        $this->addSql('ALTER TABLE challenge_rule CHANGE challenge_id challenge_id INT NOT NULL');
        $this->addSql('ALTER TABLE video CHANGE status status VARCHAR(8) NOT NULL');
    }
}
