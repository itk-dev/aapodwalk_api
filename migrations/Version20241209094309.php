<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241209094309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE point_of_interest ADD route_id INT');
        // Set route on points. We use `MAX` to make sure that we only get one route per point (in the unlikely event that a point has been used in multiple routes).
        $this->addSql('UPDATE point_of_interest SET route_id = (SELECT MAX(route_id) FROM route_point_of_interest WHERE route_point_of_interest.point_of_interest_id = point_of_interest.id)');
        // Delete orphaned points.
        $this->addSql('DELETE FROM point_of_interest WHERE route_id IS NULL');
        $this->addSql('ALTER TABLE point_of_interest CHANGE route_id route_id INT NOT NULL');
        $this->addSql('ALTER TABLE point_of_interest ADD CONSTRAINT FK_E67AD35934ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id)');
        $this->addSql('CREATE INDEX IDX_E67AD35934ECB4E6 ON point_of_interest (route_id)');

        $this->addSql('ALTER TABLE route_point_of_interest DROP FOREIGN KEY FK_3B233DFE1FE9DE17');
        $this->addSql('ALTER TABLE route_point_of_interest DROP FOREIGN KEY FK_3B233DFE34ECB4E6');
        $this->addSql('DROP TABLE route_point_of_interest');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE point_of_interest DROP FOREIGN KEY FK_E67AD35934ECB4E6');
        $this->addSql('DROP INDEX IDX_E67AD35934ECB4E6 ON point_of_interest');
        $this->addSql('ALTER TABLE point_of_interest DROP route_id');

        $this->addSql('CREATE TABLE route_point_of_interest (route_id INT NOT NULL, point_of_interest_id INT NOT NULL, INDEX IDX_3B233DFE34ECB4E6 (route_id), INDEX IDX_3B233DFE1FE9DE17 (point_of_interest_id), PRIMARY KEY(route_id, point_of_interest_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE route_point_of_interest ADD CONSTRAINT FK_3B233DFE1FE9DE17 FOREIGN KEY (point_of_interest_id) REFERENCES point_of_interest (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE route_point_of_interest ADD CONSTRAINT FK_3B233DFE34ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id) ON DELETE CASCADE');
    }
}
