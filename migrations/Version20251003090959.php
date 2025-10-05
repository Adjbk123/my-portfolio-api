<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251003090959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles_blog DROP FOREIGN KEY FK_9AE9960460BB6FE6');
        $this->addSql('DROP INDEX IDX_9AE9960460BB6FE6 ON articles_blog');
        $this->addSql('ALTER TABLE articles_blog DROP auteur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles_blog ADD auteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE articles_blog ADD CONSTRAINT FK_9AE9960460BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9AE9960460BB6FE6 ON articles_blog (auteur_id)');
    }
}
