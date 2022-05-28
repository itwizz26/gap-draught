<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220527081943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalogue ADD member_id INT DEFAULT NULL, CHANGE username member_name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE catalogue ADD CONSTRAINT FK_59A699F57597D3FE FOREIGN KEY (member_id) REFERENCES members (id)');
        $this->addSql('CREATE INDEX IDX_59A699F57597D3FE ON catalogue (member_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalogue DROP FOREIGN KEY FK_59A699F57597D3FE');
        $this->addSql('DROP INDEX IDX_59A699F57597D3FE ON catalogue');
        $this->addSql('ALTER TABLE catalogue DROP member_id, CHANGE member_name username VARCHAR(50) NOT NULL');
    }
}
