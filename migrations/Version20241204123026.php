<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241204123026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_user DROP FOREIGN KEY FK_AC64A0BA896DBBDE');
        $this->addSql('ALTER TABLE api_user DROP FOREIGN KEY FK_AC64A0BAB03A8386');
        $this->addSql('DROP TABLE api_user');
        $this->addSql('ALTER TABLE user ADD api_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497BA2F5EB ON user (api_token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE api_user (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', token VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_AC64A0BA896DBBDE (updated_by_id), UNIQUE INDEX UNIQ_AC64A0BA5F37A13B (token), INDEX IDX_AC64A0BAB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE api_user ADD CONSTRAINT FK_AC64A0BA896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE api_user ADD CONSTRAINT FK_AC64A0BAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX UNIQ_8D93D6497BA2F5EB ON user');
        $this->addSql('ALTER TABLE user DROP api_token');
    }
}
