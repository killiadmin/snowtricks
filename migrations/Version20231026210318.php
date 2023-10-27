<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231026210318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD user_associated_id INT DEFAULT NULL, ADD figure_associated_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4DC95A3E FOREIGN KEY (user_associated_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7311B2FA FOREIGN KEY (figure_associated_id) REFERENCES figure (id)');
        $this->addSql('CREATE INDEX IDX_9474526C4DC95A3E ON comment (user_associated_id)');
        $this->addSql('CREATE INDEX IDX_9474526C7311B2FA ON comment (figure_associated_id)');
        $this->addSql('ALTER TABLE figure ADD user_associated_id INT NOT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37A4DC95A3E FOREIGN KEY (user_associated_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2F57B37A4DC95A3E ON figure (user_associated_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4DC95A3E');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C7311B2FA');
        $this->addSql('DROP INDEX IDX_9474526C4DC95A3E ON comment');
        $this->addSql('DROP INDEX IDX_9474526C7311B2FA ON comment');
        $this->addSql('ALTER TABLE comment DROP user_associated_id, DROP figure_associated_id');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37A4DC95A3E');
        $this->addSql('DROP INDEX IDX_2F57B37A4DC95A3E ON figure');
        $this->addSql('ALTER TABLE figure DROP user_associated_id');
    }
}
