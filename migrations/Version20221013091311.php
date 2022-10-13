<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221013091311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE command CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE live CHANGE video_status video_status varchar(8) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE command CHANGE category_id category_id INT NOT NULL');
        $this->addSql('ALTER TABLE live CHANGE video_status video_status VARCHAR(8) DEFAULT NULL');
    }
}
