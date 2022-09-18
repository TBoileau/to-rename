<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220918121057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, live_id INT NOT NULL, video_id INT DEFAULT NULL, description LONGTEXT NOT NULL, started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ended_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', base_points INT NOT NULL, duration_hours INT NOT NULL, duration_minutes INT NOT NULL, duration_seconds INT NOT NULL, INDEX IDX_D70989511DEBA901 (live_id), INDEX IDX_D709895129C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE challenge_rule (id INT AUTO_INCREMENT NOT NULL, challenge_id INT NOT NULL, rule_id INT NOT NULL, hit INT NOT NULL, INDEX IDX_4F77AB4A98A21AC6 (challenge_id), INDEX IDX_4F77AB4A744E0351 (rule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rule (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, points INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D70989511DEBA901 FOREIGN KEY (live_id) REFERENCES live (id)');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D709895129C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE challenge_rule ADD CONSTRAINT FK_4F77AB4A98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
        $this->addSql('ALTER TABLE challenge_rule ADD CONSTRAINT FK_4F77AB4A744E0351 FOREIGN KEY (rule_id) REFERENCES rule (id)');
        $this->addSql('ALTER TABLE video CHANGE status status varchar(8) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D70989511DEBA901');
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D709895129C1004E');
        $this->addSql('ALTER TABLE challenge_rule DROP FOREIGN KEY FK_4F77AB4A98A21AC6');
        $this->addSql('ALTER TABLE challenge_rule DROP FOREIGN KEY FK_4F77AB4A744E0351');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE challenge_rule');
        $this->addSql('DROP TABLE rule');
        $this->addSql('ALTER TABLE video CHANGE status status VARCHAR(8) NOT NULL');
    }
}
