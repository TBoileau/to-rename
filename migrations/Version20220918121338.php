<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220918121338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge_rule DROP FOREIGN KEY FK_4F77AB4A744E0351');
        $this->addSql('ALTER TABLE challenge_rule ADD CONSTRAINT FK_4F77AB4A744E0351 FOREIGN KEY (rule_id) REFERENCES rule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video CHANGE status status varchar(8) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge_rule DROP FOREIGN KEY FK_4F77AB4A744E0351');
        $this->addSql('ALTER TABLE challenge_rule ADD CONSTRAINT FK_4F77AB4A744E0351 FOREIGN KEY (rule_id) REFERENCES rule (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE video CHANGE status status VARCHAR(8) NOT NULL');
    }
}
