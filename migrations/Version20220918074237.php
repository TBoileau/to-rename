<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220918074237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE video ADD views INT NOT NULL, ADD likes INT NOT NULL, ADD comments INT NOT NULL, CHANGE status status varchar(8) NOT NULL');
        $this->addSql('ALTER TABLE video RENAME INDEX idx_7cc7da2cf98f144a TO IDX_7CC7DA2C12469DE2');
        $this->addSql('UPDATE video SET views = 0, likes = 0, comments = 0 WHERE 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE video DROP views, DROP likes, DROP comments, CHANGE status status VARCHAR(8) NOT NULL');
        $this->addSql('ALTER TABLE video RENAME INDEX idx_7cc7da2c12469de2 TO IDX_7CC7DA2CF98F144A');
    }
}
