<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221015114457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, scheduled_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter_posts (newsletter_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_230F60E22DB1917 (newsletter_id), INDEX IDX_230F60E4B89032C (post_id), PRIMARY KEY(newsletter_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter_lives (newsletter_id INT NOT NULL, live_id INT NOT NULL, INDEX IDX_D75932AA22DB1917 (newsletter_id), INDEX IDX_D75932AA1DEBA901 (live_id), PRIMARY KEY(newsletter_id, live_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE newsletter_posts ADD CONSTRAINT FK_230F60E22DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE newsletter_posts ADD CONSTRAINT FK_230F60E4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE newsletter_lives ADD CONSTRAINT FK_D75932AA22DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE newsletter_lives ADD CONSTRAINT FK_D75932AA1DEBA901 FOREIGN KEY (live_id) REFERENCES live (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE live CHANGE video_status video_status varchar(8) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE newsletter_posts DROP FOREIGN KEY FK_230F60E22DB1917');
        $this->addSql('ALTER TABLE newsletter_posts DROP FOREIGN KEY FK_230F60E4B89032C');
        $this->addSql('ALTER TABLE newsletter_lives DROP FOREIGN KEY FK_D75932AA22DB1917');
        $this->addSql('ALTER TABLE newsletter_lives DROP FOREIGN KEY FK_D75932AA1DEBA901');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE newsletter_posts');
        $this->addSql('DROP TABLE newsletter_lives');
        $this->addSql('ALTER TABLE live CHANGE video_status video_status VARCHAR(8) DEFAULT NULL');
    }
}
