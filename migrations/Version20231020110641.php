<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231020110641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create entities User';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON DEFAULT NULL, password VARCHAR(255) NOT NULL, name_identifier VARCHAR(255) DEFAULT NULL, firstname_identifier VARCHAR(255) DEFAULT NULL, pseudo VARCHAR(255) NOT NULL, activated TINYINT(1) DEFAULT NULL, picture_identifier VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // Create default user
        $this->addSql("INSERT INTO user (email, roles, password, name_identifier, firstname_identifier, pseudo, activated, picture_identifier) VALUES ('k.filatre@snowtricks.fr', '".json_encode(['ROLES_ADMIN'])."', '$2y$10$/mXnNxvxqPns9hDAq0KNl.s2eVFe6hI3KbRg4sx3Tzcz/DD1D/pDq', 'Filatre', 'Killian', 'killiadmin', 1, 'default_avatar.webp')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
