<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241204145627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE route_tag (route_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_210074E934ECB4E6 (route_id), INDEX IDX_210074E9BAD26311 (tag_id), PRIMARY KEY(route_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_389B783B03A8386 (created_by_id), INDEX IDX_389B783896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE route_tag ADD CONSTRAINT FK_210074E934ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE route_tag ADD CONSTRAINT FK_210074E9BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE route_tags DROP FOREIGN KEY FK_C2F995A334ECB4E6');
        $this->addSql('ALTER TABLE route_tags DROP FOREIGN KEY FK_C2F995A38D7B4FB4');
        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_6FBC9426896DBBDE');
        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_6FBC9426B03A8386');
        $this->addSql('INSERT INTO tag(id, created_by_id, updated_by_id, name, created_at, updated_at) SELECT id, created_by_id, updated_by_id, name, created_at, updated_at from tags');
        $this->addSql('INSERT INTO route_tag(route_id, tag_id) SELECT route_id, tags_id from route_tags');
        $this->addSql('DROP TABLE route_tags');
        $this->addSql('DROP TABLE tags');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE route_tags (route_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_C2F995A38D7B4FB4 (tags_id), INDEX IDX_C2F995A334ECB4E6 (route_id), PRIMARY KEY(route_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6FBC9426896DBBDE (updated_by_id), INDEX IDX_6FBC9426B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE route_tags ADD CONSTRAINT FK_C2F995A334ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE route_tags ADD CONSTRAINT FK_C2F995A38D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_6FBC9426896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_6FBC9426B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE route_tag DROP FOREIGN KEY FK_210074E934ECB4E6');
        $this->addSql('ALTER TABLE route_tag DROP FOREIGN KEY FK_210074E9BAD26311');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783B03A8386');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783896DBBDE');
        $this->addSql('INSERT INTO tags(id, created_by_id, updated_by_id, name, created_at, updated_at) SELECT id, created_by_id, updated_by_id, name, created_at, updated_at from tag');
        $this->addSql('INSERT INTO route_tags(route_id, tags_id) SELECT route_id, tag_id from route_tag');
        $this->addSql('DROP TABLE route_tag');
        $this->addSql('DROP TABLE tag');
    }
}
