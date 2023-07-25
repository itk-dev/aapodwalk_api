<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230725113741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE point_of_interest ADD poi_order INT NOT NULL');
        $this->addSql('ALTER TABLE route ADD zoom_value VARCHAR(255) NOT NULL, ADD centerlatitude VARCHAR(255) NOT NULL, ADD centerlongitude VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE point_of_interest DROP poi_order');
        $this->addSql('ALTER TABLE route DROP zoom_value, DROP centerlatitude, DROP centerlongitude');
    }
}
