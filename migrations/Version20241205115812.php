<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205115812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE route ADD total_duration VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE route SET total_duration = totalduration');
        $this->addSql('ALTER TABLE route DROP zoom_value, DROP centerlatitude, DROP centerlongitude, DROP partcount, DROP totalduration');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE route ADD totalduration VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE route SET totalduration = total_duration');
        $this->addSql('ALTER TABLE route ADD centerlatitude VARCHAR(255) NOT NULL, ADD centerlongitude VARCHAR(255) NOT NULL, ADD partcount VARCHAR(255) NOT NULL, ADD zoom_value VARCHAR(255) NOT NULL, DROP total_duration');
    }
}
