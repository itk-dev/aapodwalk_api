<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230718131644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE api_user (id INT AUTO_INCREMENT NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', token VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_AC64A0BA5F37A13B (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE point_of_interest (id INT AUTO_INCREMENT NOT NULL, subtitles VARCHAR(10000) NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, podcast VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE route (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, distance VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE route_point_of_interest (route_id INT NOT NULL, point_of_interest_id INT NOT NULL, INDEX IDX_3B233DFE34ECB4E6 (route_id), INDEX IDX_3B233DFE1FE9DE17 (point_of_interest_id), PRIMARY KEY(route_id, point_of_interest_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE route_tags (route_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_C2F995A334ECB4E6 (route_id), INDEX IDX_C2F995A38D7B4FB4 (tags_id), PRIMARY KEY(route_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE route_point_of_interest ADD CONSTRAINT FK_3B233DFE34ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE route_point_of_interest ADD CONSTRAINT FK_3B233DFE1FE9DE17 FOREIGN KEY (point_of_interest_id) REFERENCES point_of_interest (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE route_tags ADD CONSTRAINT FK_C2F995A334ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE route_tags ADD CONSTRAINT FK_C2F995A38D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE route_point_of_interest DROP FOREIGN KEY FK_3B233DFE34ECB4E6');
        $this->addSql('ALTER TABLE route_point_of_interest DROP FOREIGN KEY FK_3B233DFE1FE9DE17');
        $this->addSql('ALTER TABLE route_tags DROP FOREIGN KEY FK_C2F995A334ECB4E6');
        $this->addSql('ALTER TABLE route_tags DROP FOREIGN KEY FK_C2F995A38D7B4FB4');
        $this->addSql('DROP TABLE api_user');
        $this->addSql('DROP TABLE point_of_interest');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP TABLE route_point_of_interest');
        $this->addSql('DROP TABLE route_tags');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE user');
    }
}
