<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220916113320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE live (id INT AUTO_INCREMENT NOT NULL, planning_id INT NOT NULL, lived_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description LONGTEXT NOT NULL, INDEX IDX_530F2CAF3D865311 (planning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logo (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, started_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', ended_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE token (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, refresh_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, logo_id INT DEFAULT NULL, live_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, season INT NOT NULL, episode INT NOT NULL, description LONGTEXT NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, thumbnails JSON NOT NULL, youtube_id VARCHAR(255) NOT NULL, tags JSON NOT NULL, status varchar(8) NOT NULL, INDEX IDX_7CC7DA2CF98F144A (logo_id), INDEX IDX_7CC7DA2C1DEBA901 (live_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE live ADD CONSTRAINT FK_530F2CAF3D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CF98F144A FOREIGN KEY (logo_id) REFERENCES logo (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C1DEBA901 FOREIGN KEY (live_id) REFERENCES live (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE live DROP FOREIGN KEY FK_530F2CAF3D865311');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2CF98F144A');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C1DEBA901');
        $this->addSql('DROP TABLE live');
        $this->addSql('DROP TABLE logo');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE video');
    }
}
