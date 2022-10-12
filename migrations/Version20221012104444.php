<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221012104444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, template LONGTEXT NOT NULL, parameters JSON NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, started_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ended_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', base_points INT NOT NULL, duration_hours INT NOT NULL, duration_minutes INT NOT NULL, duration_seconds INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE challenge_rule (id INT AUTO_INCREMENT NOT NULL, challenge_id INT DEFAULT NULL, rule_id INT NOT NULL, hits INT NOT NULL, INDEX IDX_4F77AB4A98A21AC6 (challenge_id), INDEX IDX_4F77AB4A744E0351 (rule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', parameters JSON NOT NULL, INDEX IDX_FEC530A912469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE live (id INT AUTO_INCREMENT NOT NULL, planning_id INT NOT NULL, content_id INT NOT NULL, thumbnail VARCHAR(255) NOT NULL, lived_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', season INT NOT NULL, episode INT NOT NULL, youtube_id VARCHAR(255) DEFAULT NULL, duration_hours INT NOT NULL, duration_minutes INT NOT NULL, duration_seconds INT NOT NULL, video_status varchar(8) DEFAULT NULL, video_views INT DEFAULT NULL, video_likes INT DEFAULT NULL, video_comments INT DEFAULT NULL, INDEX IDX_530F2CAF3D865311 (planning_id), INDEX IDX_530F2CAF84A0A3ED (content_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, started_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', ended_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rule (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, points INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE token (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, refresh_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE challenge_rule ADD CONSTRAINT FK_4F77AB4A98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
        $this->addSql('ALTER TABLE challenge_rule ADD CONSTRAINT FK_4F77AB4A744E0351 FOREIGN KEY (rule_id) REFERENCES rule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A912469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE live ADD CONSTRAINT FK_530F2CAF3D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE live ADD CONSTRAINT FK_530F2CAF84A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge_rule DROP FOREIGN KEY FK_4F77AB4A98A21AC6');
        $this->addSql('ALTER TABLE challenge_rule DROP FOREIGN KEY FK_4F77AB4A744E0351');
        $this->addSql('ALTER TABLE content DROP FOREIGN KEY FK_FEC530A912469DE2');
        $this->addSql('ALTER TABLE live DROP FOREIGN KEY FK_530F2CAF3D865311');
        $this->addSql('ALTER TABLE live DROP FOREIGN KEY FK_530F2CAF84A0A3ED');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE challenge_rule');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE live');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE rule');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE user');
    }
}
