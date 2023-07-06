<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230706081012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE route_tags (route_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_C2F995A334ECB4E6 (route_id), INDEX IDX_C2F995A38D7B4FB4 (tags_id), PRIMARY KEY(route_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE route_tags ADD CONSTRAINT FK_C2F995A334ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE route_tags ADD CONSTRAINT FK_C2F995A38D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tags_route DROP FOREIGN KEY FK_69A0D62134ECB4E6');
        $this->addSql('ALTER TABLE tags_route DROP FOREIGN KEY FK_69A0D6218D7B4FB4');
        $this->addSql('DROP TABLE tags_route');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tags_route (tags_id INT NOT NULL, route_id INT NOT NULL, INDEX IDX_69A0D62134ECB4E6 (route_id), INDEX IDX_69A0D6218D7B4FB4 (tags_id), PRIMARY KEY(tags_id, route_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tags_route ADD CONSTRAINT FK_69A0D62134ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tags_route ADD CONSTRAINT FK_69A0D6218D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE route_tags DROP FOREIGN KEY FK_C2F995A334ECB4E6');
        $this->addSql('ALTER TABLE route_tags DROP FOREIGN KEY FK_C2F995A38D7B4FB4');
        $this->addSql('DROP TABLE route_tags');
    }
}
