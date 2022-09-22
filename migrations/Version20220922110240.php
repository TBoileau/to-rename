<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220922110240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE challenge_rule (id INT AUTO_INCREMENT NOT NULL, challenge_id INT DEFAULT NULL, rule_id INT NOT NULL, hits INT NOT NULL, INDEX IDX_4F77AB4A98A21AC6 (challenge_id), INDEX IDX_4F77AB4A744E0351 (rule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content (id INT AUTO_INCREMENT NOT NULL, live_id INT DEFAULT NULL, video_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, discr VARCHAR(255) NOT NULL, started_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ended_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', base_points INT DEFAULT NULL, repository VARCHAR(255) DEFAULT NULL, duration_hours INT DEFAULT NULL, duration_minutes INT DEFAULT NULL, duration_seconds INT DEFAULT NULL, INDEX IDX_FEC530A91DEBA901 (live_id), INDEX IDX_FEC530A929C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE live (id INT AUTO_INCREMENT NOT NULL, planning_id INT NOT NULL, content_id INT DEFAULT NULL, lived_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description LONGTEXT NOT NULL, duration_hours INT NOT NULL, duration_minutes INT NOT NULL, duration_seconds INT NOT NULL, INDEX IDX_530F2CAF3D865311 (planning_id), INDEX IDX_530F2CAF84A0A3ED (content_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, started_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', ended_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rule (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, points INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE token (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, refresh_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, live_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, season INT NOT NULL, episode INT NOT NULL, description LONGTEXT NOT NULL, thumbnail VARCHAR(255) NOT NULL, youtube_id VARCHAR(255) NOT NULL, tags JSON NOT NULL, status varchar(8) NOT NULL, views INT NOT NULL, likes INT NOT NULL, comments INT NOT NULL, INDEX IDX_7CC7DA2C12469DE2 (category_id), INDEX IDX_7CC7DA2C1DEBA901 (live_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE challenge_rule ADD CONSTRAINT FK_4F77AB4A98A21AC6 FOREIGN KEY (challenge_id) REFERENCES content (id)');
        $this->addSql('ALTER TABLE challenge_rule ADD CONSTRAINT FK_4F77AB4A744E0351 FOREIGN KEY (rule_id) REFERENCES rule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A91DEBA901 FOREIGN KEY (live_id) REFERENCES live (id)');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A929C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE live ADD CONSTRAINT FK_530F2CAF3D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE live ADD CONSTRAINT FK_530F2CAF84A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C1DEBA901 FOREIGN KEY (live_id) REFERENCES live (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge_rule DROP FOREIGN KEY FK_4F77AB4A98A21AC6');
        $this->addSql('ALTER TABLE challenge_rule DROP FOREIGN KEY FK_4F77AB4A744E0351');
        $this->addSql('ALTER TABLE content DROP FOREIGN KEY FK_FEC530A91DEBA901');
        $this->addSql('ALTER TABLE content DROP FOREIGN KEY FK_FEC530A929C1004E');
        $this->addSql('ALTER TABLE live DROP FOREIGN KEY FK_530F2CAF3D865311');
        $this->addSql('ALTER TABLE live DROP FOREIGN KEY FK_530F2CAF84A0A3ED');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C12469DE2');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C1DEBA901');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE challenge_rule');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE live');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE rule');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE video');
    }
}
